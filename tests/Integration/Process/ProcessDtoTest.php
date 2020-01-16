<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\Exception\PipesFrameworkException;
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
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeader
     *
     * @throws PipesFrameworkException
     */
    public function testSetRepeator(): void
    {
        $processDto = (new ProcessDto())
            ->setRepeater(10, 20, 15, 'queue')
            ->setData('data');

        self::assertEquals($this->getSetRepeaterHeaders(), $processDto->getHeaders());
        self::assertEquals('data', $processDto->getData());

        $processDto->setHeaders(
            [
                'pf-repeat-interval' => '5',
                'pf-repeat-max-hops' => '10',
            ]
        );
        self::assertEquals(
            [
                'pf-repeat-interval' => '5',
                'pf-repeat-max-hops' => '10',
            ],
            $processDto->getHeaders()
        );

        self::assertEquals(5, $processDto->getHeader('pf-repeat-interval'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setSuccessProcess
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setStatusHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::validateStatus
     *
     * @throws PipesFrameworkException
     */
    public function testProcess(): void
    {
        $processDto = (new ProcessDto())->setSuccessProcess('it is ok');

        self::assertEquals(
            [
                'pf-result-message' => 'it is ok',
                'pf-result-code'    => '0',
            ],
            $processDto->getHeaders()
        );

        self::expectException(PipesFrameworkException::class);
        $processDto->setStopProcess(5_555);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws PipesFrameworkException
     */
    public function testSetRepeaterIntervalErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(-1, 1);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws PipesFrameworkException
     */
    public function testSetRepeaterHopsErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(1, -1);
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
