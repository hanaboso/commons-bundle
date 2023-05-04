<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Process;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Hanaboso\CommonsBundle\Process\BatchProcessDto;

/**
 * Class BatchProcessDtoTest
 *
 * @package CommonsBundleTests\Integration\Process
 */
final class BatchProcessDtoTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::setBatchCursor
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::getBatchCursor
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::removeBatchCursor
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::removeRelatedHeaders
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
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::setBatchCursor
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::removeBatchCursor
     * @covers \Hanaboso\CommonsBundle\Process\BatchProcessDto::removeRelatedHeaders
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
