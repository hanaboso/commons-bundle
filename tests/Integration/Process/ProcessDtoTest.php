<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Process\ProcessDtoAbstract;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
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

        $processDto->setStopProcess(ProcessDtoAbstract::DO_NOT_CONTINUE, 'nok');
        $headers[] = $processDto->getHeaders();

        $processDto->setStopProcess(ProcessDtoAbstract::STOP_AND_FAILED, 'nok');
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
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeRepeater
     *
     * @throws Exception
     */
    public function testSetRepeater(): void
    {
        $processDto = (new ProcessDto())
            ->setRepeater(10, 20, 'queue')
            ->setData('data');

        self::assertEquals($this->getSetRepeaterHeaders(), $processDto->getHeaders());
        self::assertEquals('data', $processDto->getData());

        $processDto->setHeaders(
            [
                'repeat-interval' => '5',
                'repeat-max-hops' => '10',
            ],
        );
        self::assertEquals(
            [
                'repeat-interval' => '5',
                'repeat-max-hops' => '10',
            ],
            $processDto->getHeaders(),
        );

        self::assertEquals(5, $processDto->getHeader('repeat-interval'));

        $processDto->removeRepeater();
        self::assertEmpty($processDto->getHeader(PipesHeaders::REPEAT_QUEUE));
        self::assertEmpty($processDto->getHeader(PipesHeaders::REPEAT_MAX_HOPS));
        self::assertEmpty($processDto->getHeader(PipesHeaders::REPEAT_HOPS));
        self::assertEmpty($processDto->getHeader(PipesHeaders::REPEAT_INTERVAL));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getData
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setLimiter
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setHeaders
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::getHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeHeader
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeLimiter
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::decorateLimitKey()
     *
     * @throws Exception
     */
    public function testSetLimiter(): void
    {
        $processDto = (new ProcessDto())
            ->setLimiter('testLimit', 1, 100)
            ->setData('data');

        self::assertEquals($this->getLimiterHeaders(), $processDto->getHeaders());

        $processDto->removeLimiter();
        self::assertEmpty($processDto->getHeader(PipesHeaders::LIMITER_KEY));
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
                'result-message' => 'it is ok',
                'result-code'    => '0',
            ],
            $processDto->getHeaders(),
        );

        self::expectException(PipesFrameworkException::class);
        $processDto->setStopProcess(5_555, 'nok');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws Exception
     */
    public function testSetRepeaterIntervalErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(-1, 1, 'nok');
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setRepeater
     *
     * @throws Exception
     */
    public function testSetRepeaterHopsErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(1, -1, 'nok');
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
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeHeaders
     */
    public function testRemoveHeaders(): void
    {
        $dto = new ProcessDto();
        $dto->addHeader('keyR', "\rLosos\rLos");
        $dto->removeHeaders();
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
                               'result-message' => 'Bobr',
                               'result-code'    => '1004',
                           ], $headers);
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setLimiterWithGroup
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeLimiter
     */
    public function testSetLimiterWithGroup(): void
    {
        $processDto = new ProcessDto();
        $processDto->setLimiterWithGroup('limiterKey', 1, 10, 'groupKey', 2, 20);
        self::assertEquals(['limiter-key' => 'limiterKey|;1;10;groupKey|;2;20'], $processDto->getHeaders());
        $processDto->removeLimiter();
        self::assertEquals([], $processDto->getHeaders());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setForceFollowers
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeForceFollowers
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeRelatedHeaders
     */
    public function testSetForceFollowers(): void
    {
        $processDto = new ProcessDto();
        $processDto->addHeader(
            'worker-followers',
            Json::encode(
                [['name' => 'testFollower1', 'id' => '1'], ['name' => 'testFollower2', 'id' => '2']],
            ),
        );
        $processDto->setForceFollowers(['testFollower1', 'testFollower2']);
        self::assertEquals([
            'worker-followers'   => '[{"name":"testFollower1","id":"1"},{"name":"testFollower2","id":"2"}]',
            'force-target-queue' => '1,2',
            'result-message'  => 'Message will be force re-routed to [1,2] follower(s).',
            'result-code'     => '1002',
        ], $processDto->getHeaders());
        $processDto->removeForceFollowers();
        self::assertEquals([
            'worker-followers' => '[{"name":"testFollower1","id":"1"},{"name":"testFollower2","id":"2"}]',
        ], $processDto->getHeaders());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::setForceFollowers
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeForceFollowers
     * @covers \Hanaboso\CommonsBundle\Process\ProcessDto::removeRelatedHeaders
     */
    public function testSetForceFollowersFollowerNotAvailable(): void
    {
        self::expectException(PipesFrameworkException::class);
        $processDto = new ProcessDto();
        $processDto->addHeader(
            'worker-followers',
            Json::encode(
                [['name' => 'testFollower1', 'id' => '1'], ['name' => 'testFollower2', 'id' => '2']],
            ),
        );
        $processDto->setForceFollowers(['testFollower3']);
        $result = $processDto->getHeaders();
        $result;
    }

    /**
     * @return mixed[]
     */
    private function getSetStopProcessHeaders(): array
    {
        return [
            [PipesHeaders::RESULT_CODE => (string) ProcessDtoAbstract::DO_NOT_CONTINUE, 'result-message' => 'nok'],
            [PipesHeaders::RESULT_CODE => (string) ProcessDtoAbstract::STOP_AND_FAILED, 'result-message' => 'nok'],
        ];
    }

    /**
     * @return mixed[]
     */
    private function getSetRepeaterHeaders(): array
    {
        return [
            PipesHeaders::RESULT_CODE => (string) ProcessDtoAbstract::REPEAT,
            'repeat-interval'     => '10',
            'repeat-max-hops'     => '20',
            'result-message'      => 'queue',
        ];
    }

    /**
     * @return string[]
     */
    private function getLimiterHeaders(): array
    {
        return [
            'limiter-key' => 'testLimit|;1;100',
        ];
    }

}
