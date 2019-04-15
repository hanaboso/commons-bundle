<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Imap;

use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Utils\DateTimeUtils;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use Throwable;

/**
 * Class ImapConnector
 *
 * @package Hanaboso\CommonsBundle\Transport\Imap
 */
class ImapConnector
{

    public const ID           = 'id';
    public const TIME         = 'time';
    public const FROM_ADDRESS = 'fromAddress';
    public const SUBJECT      = 'subject';

    /**
     * @var Mailbox
     */
    private $mailbox;

    /**
     * @param ImapConfigDto $dto
     *
     * @return ImapConnector
     * @throws Throwable
     */
    public function getConnector(ImapConfigDto $dto): ImapConnector
    {
        $this->mailbox = new Mailbox(
            sprintf('{%s:993%s}%s', $dto->getHost(), $dto->getPath(), $dto->getFolder()),
            $dto->getUser(),
            $dto->getPassword());

        return $this;
    }

    /**
     * @return array
     * @throws DateTimeException
     */
    public function getListOfMails(): array
    {
        $mailIds = $this->mailbox->searchMailbox();
        $mails   = [];

        foreach ($mailIds as $mailId) {
            $mail = $this->mailbox->getMail($mailId);

            $mails[] = [
                self::ID           => (string) $mail->id,
                self::TIME         => DateTimeUtils::getUTCDateTime($mail->date),
                self::FROM_ADDRESS => $mail->fromAddress,
                self::SUBJECT      => $mail->subject ?? '',
            ];
        }

        return $mails;

    }

    /**
     * @param string $mailId
     *
     * @return IncomingMail
     */
    public function getMail(string $mailId): IncomingMail
    {
        return $this->mailbox->getMail($mailId);
    }

    /**
     * @param string $mailId
     */
    public function deleteMail(string $mailId): void
    {
        $this->mailbox->deleteMail($mailId);
    }

    /**
     * @param string $mailId
     * @param string $destination
     */
    public function moveMail(string $mailId, string $destination): void
    {
        $this->mailbox->moveMail($mailId, $this->checkMailbox($destination));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function checkMailbox(string $name): string
    {
        $mailboxes = $this->mailbox->getMailboxes(sprintf('*%s*', $name));

        if (!$mailboxes) {
            $this->mailbox->createMailbox($name);

            return $this->mailbox->getMailboxes(sprintf('*%s*', $name))[0]['shortpath'];
        }

        return $mailboxes[0]['shortpath'];
    }

}