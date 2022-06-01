<?php declare(strict_types=1);

namespace CommonsBundleTests\Unit\Transport\Imap;

use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConfigDto;
use Hanaboso\CommonsBundle\Transport\Imap\ImapConnector;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailHeader;
use PhpImap\Mailbox;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ImapConnectorTest
 *
 * @package CommonsBundleTests\Unit\Transport\Imap
 */
final class ImapConnectorTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    /**
     * @var Mailbox|MockObject
     */
    private $mailbox;

    /**
     * @var ImapConnector
     */
    private ImapConnector $connector;

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::create
     * @throws Exception
     */
    public function testCreate(): void
    {
        $this->connector->create($this->getImapConfig());

        self::assertEmpty([]);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::listMails
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::getMail
     *
     * @throws Exception
     */
    public function testListMails(): void
    {
        $headers              = new IncomingMailHeader();
        $headers->id          = 1;
        $headers->date        = 'now';
        $headers->fromAddress = 'from@adrress.com';

        $mail = new IncomingMail();
        $mail->setHeader($headers);

        $this->mockImapMailBox(
            [
                'searchMailbox' => [1, 2],
                'getMail'       => $mail,
            ],
        );

        self::assertEquals(2, count($this->connector->listMails()));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::deleteMail
     *
     * @throws Exception
     */
    public function testDeleteMail(): void
    {
        $this->mockImapMailBox(['deleteMail' => NULL]);

        $this->connector->deleteMail(1);
        self::assertFake();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::moveMail
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::checkMailbox
     *
     * @throws Exception
     */
    public function testMoveMail(): void
    {
        $this->mockImapMailBox(['moveMail' => NULL, 'getMailboxes' => [['shortpath' => 'path']]]);

        $this->connector->moveMail(1, '/path');
        self::assertFake();
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Transport\Imap\ImapConnector::checkMailbox
     * @throws Exception
     */
    public function testCheckMailBox(): void
    {
        $this->mailbox
            ->expects(self::exactly(2))
            ->method('getMailboxes')
            ->willReturnOnConsecutiveCalls([], [['shortpath' => 'path']]);
        $this->mailbox
            ->expects(self::any())
            ->method('createMailbox')
            ->willReturnCallback(
                static function (): void {
                },
            );
        $this->mailbox
            ->expects(self::any())
            ->method('moveMail')
            ->willReturnCallback(
                static function (): void {
                },
            );
        $this->setProperty($this->connector, 'mailbox', $this->mailbox);

        $this->connector->moveMail(1, '/path/');
        self::assertFake();
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mailbox   = self::createMock(Mailbox::class);
        $this->connector = new ImapConnector();
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
     * @throws Exception
     */
    private function mockImapMailBox(array $fns): void
    {
        foreach ($fns as $key => $value) {
            if ($value === NULL) {
                $this->mailbox->expects(self::any())->method($key)->willReturnCallback(
                    static function (): void {
                    },
                );
            } else {
                $this->mailbox->expects(self::any())->method($key)->willReturn($value);
            }
        }

        $this->setProperty($this->connector, 'mailbox', $this->mailbox);
    }

}
