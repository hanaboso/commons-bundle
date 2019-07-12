<?php declare(strict_types=1);

namespace Tests\Integration\Process;

use Hanaboso\CommonsBundle\Process\ProcessDto;
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

    private const REPEAT             = 1001;
    private const DO_NOT_CONTINUE    = 1003;
    private const SPLITTER_BATCH_END = 1005;
    private const STOP_AND_FAILED    = 1006;

    private const PF_RESULT_CODE = 'pf-result-code';

    /**
     * @throws \Exception
     */
    public function testSetStopProcess(): void
    {

        $processDto = new ProcessDto();

        $processDto->setStopProcess(self::DO_NOT_CONTINUE);
        $headers[] = $processDto->getHeaders();

        $processDto->setStopProcess(self::SPLITTER_BATCH_END);
        $headers[] = $processDto->getHeaders();

        $processDto->setStopProcess(self::STOP_AND_FAILED);
        $headers[] = $processDto->getHeaders();

        self::assertEquals($this->getSetStopProcessHeaders(), $headers);

    }

    /**
     * @throws \Exception
     */
    public function testSetRepeator()
    {

        $processDto = new ProcessDto();

        $processDto->setRepeater(SELF::REPEAT, 20, 15, 'queue');

        self::assertEquals($this->getSetRepeaterHeaders(), $processDto->getHeaders());

    }

    /**
     * @return array
     */
    private function getSetStopProcessHeaders(): array
    {
        return [
            [self::PF_RESULT_CODE => (string) SELF::DO_NOT_CONTINUE],
            [self::PF_RESULT_CODE => (string) SELF::SPLITTER_BATCH_END],
            [self::PF_RESULT_CODE => (string) SELF::STOP_AND_FAILED],
        ];
    }

    /**
     * @return array
     */
    private function getSetRepeaterHeaders(): array
    {
        return [
            'pf-repeat-interval' => (string) SELF::REPEAT,
            'pf-repeat-max-hops' => '20',
            'pf-repeat-hops'     => '15',
            'pf-repeat-queue'    => (string) 'queue',
        ];
    }
}
