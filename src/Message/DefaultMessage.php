<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Message;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DefaultMessage
 *
 * @package Hanaboso\CommonsBundle\Message
 */
class DefaultMessage implements MessageInterface
{

    /**
     * @Serializer\Type("array")
     * @var array
     */
    protected $settings;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected $data;

    /**
     * DefaultMessage constructor.
     *
     * @param string $data
     * @param array  $settings
     */
    public function __construct(string $data = '', array $settings = [])
    {
        $this->settings = $settings;
        $this->data     = $data;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

}
