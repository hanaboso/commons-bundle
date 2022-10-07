.PHONY: init-dev test

DC=docker-compose
DE=docker-compose exec -T app
DM=docker-compose exec -T mariadb
DEC=docker-compose exec -T app composer

.env:
	sed -e "s/{DEV_UID}/$(shell if [ "$(shell uname)" = "Linux" ]; then echo $(shell id -u); else echo '1001'; fi)/g" \
		-e "s/{DEV_GID}/$(shell if [ "$(shell uname)" = "Linux" ]; then echo $(shell id -g); else echo '1001'; fi)/g" \
		-e "s/{SSH_AUTH}/$(shell if [ "$(shell uname)" = "Linux" ]; then echo '${SSH_AUTH_SOCK}' | sed 's/\//\\\//g'; else echo '\/run\/host-services\/ssh-auth.sock'; fi)/g" \
		.env.dist > .env; \

# Docker
docker-up-force: .env
	$(DC) build
	$(DC) pull --ignore-pull-failures
	$(DC) up -d --force-recreate --remove-orphans

docker-down-clean: .env
	$(DC) down -v

# Composer
composer-install:
	$(DE) composer install --ignore-platform-reqs
	$(DE) composer update --ignore-platform-reqs --dry-run roave/security-advisories

composer-update:
	$(DE) composer update --ignore-platform-reqs
	$(DE) composer update --ignore-platform-reqs --dry-run roave/security-advisories
	$(DE) composer normalize

composer-outdated:
	$(DE) composer outdated

# Console
clear-cache:
	$(DE) rm -rf var/log
	$(DE) php tests/testApp/bin/console cache:clear --env=test
	$(DE) php tests/testApp/bin/console cache:warmup --env=test

database-create:
	$(DE) php tests/testApp/bin/console doctrine:database:drop --force --env=test || true
	$(DE) php tests/testApp/bin/console doctrine:database:create --env=test
	$(DE) php tests/testApp/bin/console doctrine:schema:create --env=test
	for i in `seq 1 $$(nproc)`; do \
			$(DM) /bin/bash -c "mysql -uroot -proot <<< 'DROP DATABASE IF EXISTS commons$$i;'" ; \
			$(DM) /bin/bash -c "mysql -uroot -proot <<< 'CREATE DATABASE commons$$i CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'" ; \
			$(DM) /bin/bash -c "mysqldump -uroot -proot commons | mysql -uroot -proot commons$$i" ; \
	done

# App dev
init-dev: docker-up-force composer-install

phpcodesniffer:
	$(DE) ./vendor/bin/phpcs --parallel=$$(nproc) --standard=./ruleset.xml src tests

phpcodesnifferfix:
	$(DE) ./vendor/bin/phpcbf --parallel=$$(nproc) --standard=./ruleset.xml src tests

phpstan:
	$(DE) ./vendor/bin/phpstan analyse -c ./phpstan.neon -l 8 src tests

phpunit:
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p $$(nproc) tests/Unit

phpintegration: database-create
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p $$(nproc) tests/Integration

phpcontroller:
	$(DE) ./vendor/bin/phpunit -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist tests/Controller

phpcoverage:
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p $$(nproc) --coverage-html var/coverage --whitelist src --exclude-group live tests

phpcoverage-ci:
	$(DE) ./vendor/hanaboso/php-check-utils/bin/coverage.sh -p $$(nproc) -e live -c 95

test: docker-up-force composer-install fasttest

fasttest: clear-cache phpcodesniffer phpstan phpunit phpintegration phpcontroller phpcoverage-ci
