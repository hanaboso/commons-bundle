<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Utils;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class NodeServiceLoader
 *
 * @package Hanaboso\CommonsBundle\Utils
 */
final class NodeServiceLoader
{

    /**
     * @param mixed[] $dirs
     * @param string  $nodeType
     * @param mixed[] $exclude
     *
     * @return mixed[]
     */
    public static function getServices(array $dirs, string $nodeType, array $exclude = []): array
    {
        $finder = new Finder();
        $res    = [];

        foreach ($dirs as $dir) {
            $finder->name(['*.yaml'])->in($dir);

            foreach ($finder as $file) {
                $list = Yaml::parse((string) $file->getContents(), Yaml::PARSE_CUSTOM_TAGS);

                foreach (array_keys($list['services'] ?? []) as $key) {
                    if (strrpos((string) $key, $nodeType) !== 0) {
                        continue;
                    }

                    $shortened = str_replace(sprintf('%s.', $nodeType), '', (string) $key);
                    if (in_array($shortened, $exclude)) {
                        unset($exclude[$shortened]);

                        continue;
                    }
                    if (in_array($shortened, $res)) {
                        continue;
                    }
                    $res[] = $shortened;
                }
            }
        }

        sort($res);

        return $res;
    }

}
