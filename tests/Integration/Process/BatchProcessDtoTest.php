<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Process\BatchProcessDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class BatchProcessDtoTest
 *
 * @package CommonsBundleTests\Integration\Process
 */
#[CoversClass(BatchProcessDto::class)]
final class BatchProcessDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @return void
     */
    public function testSetBatchCursor(): void
    {
        $batchProcessDto = new BatchProcessDto();
        $batchProcessDto->setBatchCursor('testCursor');
        self::assertEquals('testCursor', $batchProcessDto->getBatchCursor('0'));
        self::assertEquals([
            'cursor'            => 'testCursor',
            'result-code'    => '1010',
            'result-message' => 'Message will be used as a iterator with cursor [testCursor]. Data will be send to follower(s).',
        ], $batchProcessDto->getHeaders());
        $batchProcessDto->removeBatchCursor();
        self::assertEquals([], $batchProcessDto->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetBatchCursorIterateOnly(): void
    {
        $batchProcessDto = new BatchProcessDto();
        $batchProcessDto->setBatchCursor('testCursor', TRUE);
        self::assertEquals('testCursor', $batchProcessDto->getBatchCursor('0'));
        self::assertEquals([
            'cursor'            => 'testCursor',
            'result-code'    => '1011',
            'result-message' => 'Message will be used as a iterator with cursor [testCursor]. No follower will be called.',
        ], $batchProcessDto->getHeaders());
        $batchProcessDto->removeBatchCursor();
        self::assertEquals([], $batchProcessDto->getHeaders());
    }

}
