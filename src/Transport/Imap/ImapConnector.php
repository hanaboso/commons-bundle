<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Imap;

use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;
use PhpImap\Exceptions\InvalidParameterException;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

/**
 * Class ImapConnector
 *
 * @package Hanaboso\CommonsBundle\Transport\Imap
 */
final class ImapConnector
{

    public const ID           = 'id';
    public const TIME         = 'time';
    public const FROM_ADDRESS = 'fromAddress';
    public const SUBJECT      = 'subject';

    /**
     * @var Mailbox
     */
    private Mailbox $mailbox;

    /**
     * @param ImapConfigDto $dto
     *
     * @return ImapConnector
     * @throws InvalidParameterException
     */
    public function create(ImapConfigDto $dto): self
    {
        $this->mailbox = new Mailbox(
            sprintf('{%s:993%s}%s', $dto->getHost(), $dto->getPath(), $dto->getFolder()),
            $dto->getUser(),
            $dto->getPassword(),
        );

        return $this;
    }

    /**
     * @return mixed[]
     * @throws DateTimeException
     */
    public function listMails(): array
    {
        $mailIds = $this->mailbox->searchMailbox();
        $mails   = [];

        foreach ($mailIds as $mailId) {
            $mail = $this->getMail($mailId);

            $mails[] = [
                self::FROM_ADDRESS => $mail->fromAddress,
                self::ID           => (string) $mail->id,
                self::SUBJECT      => $mail->subject ?? '',
                self::TIME         => DateTimeUtils::getUtcDateTime($mail->date ?? 'now'),
            ];
        }

        return $mails;
    }

    /**
     * @param int $mailId
     *
     * @return IncomingMail
     */
    public function getMail(int $mailId): IncomingMail
    {
        return $this->mailbox->getMail($mailId);
    }

    /**
     * @param int $mailId
     */
    public function deleteMail(int $mailId): void
    {
        $this->mailbox->deleteMail($mailId);
    }

    /**
     * @param int    $mailId
     * @param string $destination
     */
    public function moveMail(int $mailId, string $destination): void
    {
        $this->mailbox->moveMail((string) $mailId, $this->checkMailbox($destination));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function checkMailbox(string $name): string
    {
        $mailboxes = $this->mailbox->getMailboxes(sprintf('*%s*', $name));

        if (empty($mailboxes)) {
            $this->mailbox->createMailbox($name);

            return (string) $this->mailbox->getMailboxes(sprintf('*%s*', $name))[0]['shortpath'];
        }

        return (string) $mailboxes[0]['shortpath'];
    }

}
