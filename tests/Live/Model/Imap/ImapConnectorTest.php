<?php declare(strict_types=1);

namespace Tests\Live\Model\Imap;

use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConnector;
use Tests\DatabaseTestCaseAbstract;
use Tests\PrivateTrait;
use Throwable;

/**
 * Class ImapConnectorTest
 *
 * @package Tests\Live\Model\Imap
 */
final class ImapConnectorTest extends DatabaseTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @throws Throwable
     */
    public function testGetEmail(): void
    {
        $imap  = $this->getImapConnector();
        $email = $imap->getMailBox()->getMail('287');

        self::assertEquals('287', $email->id);
    }

    /**
     * @throws DateTimeException
     * @throws Throwable
     */
    public function testMoveEmail(): void
    {
        $imap = $this->getImapConnector();
        $imap->getMailBox()->moveMail('299', 'mailDestination');

        $imap->getImap()->setFolder('INBOX.mailDestination');
        $email = $imap->getMailBox()->getListOfMails();

        self::assertArrayHasKey('id', $email[0]);
    }

    /**
     * @throws DateTimeException
     * @throws Throwable
     */
    public function testGetAllEmails(): void
    {
        $imap    = $this->getImapConnector();
        $mailBox = $imap->getMailBox()->getListOfMails();

        self::assertArrayHasKey('id', $mailBox[0]);
    }

    /**
     * @return ImapConnector
     */
    private function getImapConnector(): ImapConnector
    {
        return new ImapConnector('test20180502@seznam.cz', 'qwertz789', 'imap.seznam.cz');
    }

}