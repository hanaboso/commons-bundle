<?php

namespace Hanaboso\CommonsBundle\Transport\Imap;

class ImapConfigDto
{

    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $path = '/imap/ssl/novalidate-cert';

    /**
     * @var string
     */
    private $folder = 'INBOX';

    /**
     * ImapConfigDto constructor.
     *
     * @param string $user
     * @param string $password
     * @param string $host
     */
    public function __construct(string $user, string $password, string $host)
    {
        $this->user     = $user;
        $this->password = $password;
        $this->host     = $host;
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
    public function setPath(string $path): ImapConfigDto
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $folder
     *
     * @return ImapConfigDto
     */
    public function setFolder(string $folder): ImapConfigDto
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