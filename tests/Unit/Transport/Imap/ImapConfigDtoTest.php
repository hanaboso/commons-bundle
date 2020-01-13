<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Imap;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto;

/**
 * Class ImapConfigDtoTest
 *
 * @package CommonsBundleTests\Unit\Transport\Imap
 */
final class ImapConfigDtoTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::getHost
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::getUser
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::getPath
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::getFolder
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::getPassword
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::setPath
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto::setFolder
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