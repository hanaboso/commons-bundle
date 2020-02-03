.PHONY: init-dev test

DC=docker-compose
DE=docker-compose exec -T app
DM=docker-compose exec -T mariadb
DEC=docker-compose exec -T app composer

.env:
	sed -e "s/{DEV_UID}/$(shell id -u)/g" \
		-e "s/{DEV_GID}/$(shell id -u)/g" \
		-e "s/{SSH_AUTH}/$(shell if [ "$(shell uname)" = "Linux" ]; then echo "\/tmp\/.ssh-auth-sock"; else echo '\/tmp\/.nope'; fi)/g" \
		.env.dist >> .env; \

# Docker
docker-up-force: .env
	$(DC) pull
	$(DC) up -d --force-recreate --remove-orphans

docker-down-clean: .env
	$(DC) down -v

# Composer
composer-install:
	$(DE) composer install --no-suggest

composer-update:
	$(DE) composer update --no-suggest

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
	for i in 1 2 3 4 ; do \
			$(DM) /bin/bash -c "mysql -uroot -proot <<< 'DROP DATABASE IF EXISTS commons$$i;'" ; \
			$(DM) /bin/bash -c "mysql -uroot -proot <<< 'CREATE DATABASE commons$$i CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'" ; \
			$(DM) /bin/bash -c "mysqldump -uroot -proot commons | mysql -uroot -proot commons$$i" ; \
	done

# App dev
init-dev: docker-up-force composer-install

phpcodesniffer:
	$(DE) ./vendor/bin/phpcs --standard=./ruleset.xml src tests

phpstan:
	$(DE) ./vendor/bin/phpstan analyse -c ./phpstan.neon -l 8 src tests

phpunit:
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p 4 tests/Unit

phpintegration: database-create
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p 4 tests/Integration

phpcontroller:
	$(DE) ./vendor/bin/phpunit -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist tests/Controller

phpcoverage:
	$(DE) ./vendor/bin/paratest -c ./vendor/hanaboso/php-check-utils/phpunit.xml.dist -p 4 --coverage-html var/coverage --whitelist src tests

phpcoverage-ci:
	$(DE) ./vendor/hanaboso/php-check-utils/bin/coverage.sh -p 4

test: docker-up-force composer-install fasttest

fasttest: clear-cache phpcodesniffer phpstan phpunit phpintegration phpcontroller phpcoverage-ci
