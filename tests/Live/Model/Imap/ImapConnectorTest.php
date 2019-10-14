<?php declare(strict_types=1);

namespace CommonsBundleTests\Live\Model\Imap;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConnector;
use Throwable;

/**
 * Class ImapConnectorTest
 *
 * @package CommonsBundleTests\Live\Model\Imap
 */
final class ImapConnectorTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws Throwable
     */
    public function testGetEmail(): void
    {
        $imap  = new ImapConnector();
        $email = $imap->create($this->getDto())->getMail(287);

        self::assertEquals('287', $email->id);
    }

    /**
     * @throws DateTimeException
     * @throws Throwable
     */
    public function testMoveEmail(): void
    {
        $imap = new ImapConnector();
        $imap->create($this->getDto())->moveMail(299, 'mailDestination');

        $this->getDto()->setFolder('INBOX.mailDestination');
        $email = $imap->create($this->getDto())->listMails();

        self::assertArrayHasKey('id', $email[0]);
    }

    /**
     * @throws DateTimeException
     * @throws Throwable
     */
    public function testGetAllEmails(): void
    {
        $imap    = new ImapConnector();
        $mailBox = $imap->create($this->getDto())->listMails();

        self::assertArrayHasKey('id', $mailBox[0]);
    }

    /**
     * @return ImapConfigDto
     */
    private function getDto(): ImapConfigDto
    {
        return new ImapConfigDto('test20180502@seznam.cz', 'qwertz789', 'imap.seznam.cz');
    }

}
