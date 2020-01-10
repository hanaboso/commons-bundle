<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class ProcessDtoTest
 *
 * @package CommonsBundleTests\Integration\Process
 */
final class ProcessDtoTest extends DatabaseTestCaseAbstract
{

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
     * @return mixed[]
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
     * @return mixed[]
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


