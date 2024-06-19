<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Imap;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ImapConfigDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Imap
 */
#[CoversClass(ImapConfigDto::class)]
final class ImapConfigDtoTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testImapConfigDto(): void
    {
        $config = new ImapConfigDto('guest', 'guest', 'host');

        self::assertEquals('folder', $config->setFolder('folder')->getFolder());
        self::assertEquals('path', $config->setPath('path')->getPath());
        self::assertEquals('guest', $config->getUser());
        self::assertEquals('guest', $config->getPassword());
        self::assertEquals('host', $config->getHost());
    }

}
