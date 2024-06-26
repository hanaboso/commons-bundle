<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Process\ProcessDtoAbstract;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\PipesHeaders;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class ProcessDtoTest
 *
 * @package CommonsBundleTests\Integration\Process
 */
#[CoversClass(ProcessDto::class)]
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
     * @throws Exception
     */
    public function testProcess(): void
    {
        $processDto = (new ProcessDto())->setSuccessProcess('it is ok');

        self::assertEquals(
            [
                'result-code'    => '0',
                'result-message' => 'it is ok',
            ],
            $processDto->getHeaders(),
        );

        self::expectException(PipesFrameworkException::class);
        $processDto->setStopProcess(5_555, 'nok');
    }

    /**
     * @throws Exception
     */
    public function testSetRepeaterIntervalErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(-1, 1, 'nok');
    }

    /**
     * @throws Exception
     */
    public function testSetRepeaterHopsErr(): void
    {
        self::expectException(PipesFrameworkException::class);
        (new ProcessDto())->setRepeater(1, -1, 'nok');
    }

    /**
     * @return void
     */
    public function testJsonData(): void
    {
        $dto = new ProcessDto();
        $dto->setJsonData(['key' => 'value']);
        $jsonData = $dto->getJsonData();
        self::assertEquals(['key' => 'value'], $jsonData);
    }

    /**
     * @return void
     */
    public function testRemoveHeaders(): void
    {
        $dto = new ProcessDto();
        $dto->addHeader('keyR', "\rLosos\rLos");
        $dto->removeHeaders();
        self::assertEquals([], $dto->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetLimitExceeded(): void
    {
        $dto = new ProcessDto();
        $dto->setLimitExceeded('Bobr');
        $headers = $dto->getHeaders();
        self::assertEquals([
                               'result-code'    => '1004',
                               'result-message' => 'Bobr',
                           ], $headers);
    }

    /**
     * @return void
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
     * @throws Exception
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
            'force-target-queue' => '1,2',
            'result-code'     => '1002',
            'result-message'  => 'Message will be force re-routed to [1,2] follower(s).',
            'worker-followers'   => '[{"name":"testFollower1","id":"1"},{"name":"testFollower2","id":"2"}]',
        ], $processDto->getHeaders());
        $processDto->removeForceFollowers();
        self::assertEquals([
            'worker-followers' => '[{"name":"testFollower1","id":"1"},{"name":"testFollower2","id":"2"}]',
        ], $processDto->getHeaders());
    }

    /**
     * @return void
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
            'repeat-interval'     => '10',
            'repeat-max-hops'     => '20',
            'result-message'      => 'queue',
            PipesHeaders::RESULT_CODE => (string) ProcessDtoAbstract::REPEAT,
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
