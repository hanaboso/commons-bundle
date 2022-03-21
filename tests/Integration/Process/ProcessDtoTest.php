<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\Date\DateTimeUtils;
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
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::deleteHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeRepeater
     *
     * @throws Exception
     */
    public function testSetRepeater(): void
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
            ],
        );
        self::assertEquals(
            [
                'pf-repeat-interval' => '5',
                'pf-repeat-max-hops' => '10',
            ],
            $processDto->getHeaders(),
        );

        self::assertEquals(5, $processDto->getHeader('pf-repeat-interval'));

        $processDto->removeRepeater();
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_QUEUE)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_MAX_HOPS)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_HOPS)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::REPEAT_INTERVAL)));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setLimiter
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::deleteHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeLimiter
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::decorateLimitKey()
     *
     * @throws Exception
     */
    public function testSetLimiter(): void
    {
        $now        = DateTimeUtils::getUtcDateTime();
        $processDto = (new ProcessDto())
            ->setLimiter('testLimit', 1, 100, $now)
            ->setData('data');

        self::assertEquals($this->getSetLimiterHeaders($now->getTimestamp()), $processDto->getHeaders());

        $processDto->removeLimiter();
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_KEY)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_VALUE)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_TIME)));
        self::assertEmpty($processDto->getHeader(PipesHeaders::createKey(PipesHeaders::LIMIT_LAST_UPDATE)));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setSuccessProcess
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setStatusHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::validateStatus
     *
     * @throws Exception
     */
    public function testProcess(): void
    {
        $processDto = (new ProcessDto())->setSuccessProcess('it is ok');

        self::assertEquals(
            [
                'pf-result-message' => 'it is ok',
                'pf-result-code'    => '0',
            ],
            $processDto->getHeaders(),
        );

        self::expectException(PipesFrameworkException::class);
        $processDto->setStopProcess(5_555);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws Exception
     */
    public function testSetRepeaterIntervalErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(-1, 1);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws Exception
     */
    public function testSetRepeaterHopsErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(1, -1);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::addHeader
     */
    public function testReplace(): void
    {
        $dto = new ProcessDto();
        $dto->addHeader('keyR', "\rLosos\rLos");
        $dto->addHeader('keyN', "\nLosos\nLos");
        $dto->addHeader('keyNR', "\r\nLosos\r\nLos");
        $headers = $dto->getHeaders();
        self::assertEquals(
            [
                'keyR' => ' Losos Los',
                'keyN' => ' Losos Los',
                'keyNR' => '  Losos  Los',
            ],
            $headers,
        );
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getJsonData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setJsonData
     */
    public function testJsonData(): void
    {
        $dto = new ProcessDto();
        $dto->setJsonData(['key' => 'value']);
        $jsonData = $dto->getJsonData();
        self::assertEquals(['key' => 'value'], $jsonData);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::deleteHeaders
     */
    public function testDeleteHeaders(): void
    {
        $dto = new ProcessDto();
        $dto->addHeader('keyR', "\rLosos\rLos");
        $dto->deleteHeaders();
        self::assertEquals([], $dto->getHeaders());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setLimitExceeded
     */
    public function testSetLimitExceeded(): void
    {
        $dto = new ProcessDto();
        $dto->setLimitExceeded('Bobr');
        $headers = $dto->getHeaders();
        self::assertEquals([
            'pf-result-message' => 'Bobr',
            'pf-result-code' => '1004',
        ], $headers);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::isSuccessResultCode
     */
    public function testIsSuccessResultCode(): void
    {
        $dto    = new ProcessDto();
        $result = $dto->isSuccessResultCode(0);
        self::assertEquals(TRUE, $result);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setFree
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getFree
     */
    public function testFree(): void
    {
        $dto = new ProcessDto();
        $dto->setData('Bobr');
        self::assertEquals(TRUE, $dto->getFree());
        $dto->setFree(TRUE);
        self::assertEquals('', $dto->getData());
        $dto->setFree(FALSE);
        self::assertEquals(FALSE, $dto->getFree());
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
            'pf-repeat-queue'        => 'queue',
        ];
    }

    /**
     * @param int $timestamp
     *
     * @return mixed[]
     */
    private function getSetLimiterHeaders(int $timestamp): array
    {
        return [
            'pf-limit-key'         => 'testLimit|',
            'pf-limit-time'        => '1',
            'pf-limit-value'       => '100',
            'pf-limit-last-update' => $timestamp,
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
