<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Transport\Imap;

/**
 * Class ImapConfigDto
 *
 * @package Hanaboso\CommonsBundle\Transport\Imap
 */
final class ImapConfigDto
{

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $folder;

    /**
     * ImapConfigDto constructor.
     *
     * @param string $user
     * @param string $password
     * @param string $host
     */
    public function __construct(private string $user, private string $password, private string $host)
    {
        $this->folder = 'INBOX';
        $this->path   = '/imap/ssl/novalidate-cert';
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return ImapConfigDto
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $folder
     *
     * @return ImapConfigDto
     */
    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

}
