<?php declare(strict_types=1);

namespace Tests\Integration\Process;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Utils\PipesHeaders;
use Tests\DatabaseTestCaseAbstract;
use Tests\PrivateTrait;

/**
 * Class ProcessDtoTest
 *
 * @package Tests\Integration\Process
 */
final class ProcessDtoTest extends DatabaseTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @throws Exception
     */
    public function testSetStopProcess(): void
    {

        $processDto = new ProcessDto();
        $headers    = [];

        $processDto->setStopProcess(ProcessDto::DO_NOT_CONTINUE);
        $headers[] = $processDto->getHeaders();

        $processDto->setStopProcess(ProcessDto::SPLITTER_BATCH_END);
        $headers[] = $processDto->getHeaders();

        $processDto->setStopProcess(ProcessDto::STOP_AND_FAILED);
        $headers[] = $processDto->getHeaders();

        self::assertEquals($this->getSetStopProcessHeaders(), $headers);

    }

    /**
     * @throws Exception
     */
    public function testSetRepeator(): void
    {

        $processDto = new ProcessDto();

        $processDto->setRepeater(10, 20, 15, 'queue');

        self::assertEquals($this->getSetRepeaterHeaders(), $processDto->getHeaders());

    }

    /**
     * @return array
     */
    private function getSetStopProcessHeaders(): array
    {
        return [
            [$this->getPfResultCode() => (string) ProcessDto::DO_NOT_CONTINUE],
            [$this->getPfResultCode() => (string) ProcessDto::SPLITTER_BATCH_END],
            [$this->getPfResultCode() => (string) ProcessDto::STOP_AND_FAILED],
        ];
    }

    /**
     * @return array
     */
    private function getSetRepeaterHeaders(): array
    {
        return [
            $this->getPfResultCode() => (string) ProcessDto::REPEAT,
            'pf-repeat-interval'     => '10',
            'pf-repeat-max-hops'     => '20',
            'pf-repeat-hops'         => '15',
            'pf-repeat-queue'        => (string) 'queue',
        ];
    }

    /**
     * @return string
     */
    private function getPfResultCode(): string
    {
        return PipesHeaders::createKey(PipesHeaders::RESULT_CODE);
    }

}

