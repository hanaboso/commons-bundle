<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Process;

use Hanaboso\Utils\String\Json;

/**
 * Class ProcessDto
 *
 * @package Hanaboso\CommonsBundle\Process
 */
final class ProcessDto extends ProcessDtoAbstract
{

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param mixed[] $data
     *
     * @return $this
     */
    public function setJsonData(array $data): self
    {
        $this->data = Json::encode($data);

        return $this;
    }

}
