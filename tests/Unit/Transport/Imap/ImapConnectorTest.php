<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Imap;

use CommonsBundleTests\KernelTestCaseAbstract;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConnector;
use Hanaboso\Utils\Exception\DateTimeException;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailHeader;
use PhpImap\Mailbox;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

/**
 * Class ImapConnectorTest
 *
 * @package CommonsBundleTests\Unit\Transport\Imap
 */
final class ImapConnectorTest extends KernelTestCaseAbstract
{

    /**
     * @var Mailbox|MockObject
     */
    private $mailbox;

    /**
     * @var ImapConnector
     */
    private ImapConnector $connector;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mailbox   = self::createMock(Mailbox::class);
        $this->connector = new ImapConnector();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::create
     */
    public function testCreate(): void
    {
        $imap = $this->connector->create($this->getImapConfig());

        self::assertInstanceOf(ImapConnector::class, $imap);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::listMails
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::getMail
     *
     * @throws ReflectionException
     * @throws DateTimeException
     */
    public function testListMails(): void
    {
        $headers              = new IncomingMailHeader();
        $headers->id          = '1';
        $headers->date        = 'now';
        $headers->fromAddress = 'from@adrress.com';

        $mail = new IncomingMail();
        $mail->setHeader($headers);

        $this->mockImapMailBox(
            [
                'searchMailbox' => [1, 2],
                'getMail'       => $mail,
            ]
        );

        self::assertEquals(2, count($this->connector->listMails()));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::deleteMail
     *
     * @throws ReflectionException
     */
    public function testDeleteMail(): void
    {
        $this->mockImapMailBox(['deleteMail' => TRUE]);

        $this->connector->deleteMail(1);
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::moveMail
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::checkMailbox
     *
     * @throws ReflectionException
     */
    public function testMoveMail(): void
    {
        $this->mockImapMailBox(['moveMail' => TRUE, 'getMailboxes' => [['shortpath' => 'path']]]);

        $this->connector->moveMail(1, '/path');
        self::assertTrue(TRUE);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::checkMailbox
     * @throws ReflectionException
     */
    public function testCheckMailBox(): void
    {
        $this->mailbox->expects($this->at(0))->method('getMailboxes')->willReturn([]);
        $this->mailbox->expects($this->at(2))->method('getMailboxes')->willReturn([['shortpath' => 'path']]);
        $this->mailbox->expects($this->any())->method('createMailbox')->willReturn(TRUE);
        $this->mailbox->expects($this->any())->method('moveMail')->willReturn(TRUE);
        $this->setProperty($this->connector, 'mailbox', $this->mailbox);

        $this->connector->moveMail(1, '/path/');
        self::assertTrue(TRUE);
    }

    /**
     * @return ImapConfigDto
     */
    private function getImapConfig(): ImapConfigDto
    {
        return new ImapConfigDto('guest', 'guest', 'host');
    }

    /**
     * @param mixed[] $fns
     *
     * @throws ReflectionException
     */
    private function mockImapMailBox(array $fns): void
    {
        foreach ($fns as $key => $value) {
            $this->mailbox->expects(self::any())->method($key)->willReturn($value);
        }

        $this->setProperty($this->connector, 'mailbox', $this->mailbox);
    }

}