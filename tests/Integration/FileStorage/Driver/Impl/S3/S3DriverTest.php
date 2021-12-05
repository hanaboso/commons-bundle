<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\FileStorage\Driver\Impl\S3;

use Aws\Command;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use CommonsBundleTests\KernelTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver;
use Hanaboso\Utils\File\File;
use PHPUnit\Framework\MockObject\Stub\Exception as PhpUnitException;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class S3DriverTest
 *
 * @package CommonsBundleTests\Integration\FileStorage\Driver\Impl\S3
 */
final class S3DriverTest extends KernelTestCaseAbstract
{

    private const FAKE_AWS_CONNECTION_ARGS = [
        'region'      => 's-west-2',
        'version'     => '2006-03-01',
        'credentials' => [
            'key'    => 'key',
            'secret' => 'secret',
        ],
        'endpoint'    => 'ttps://vpce-0f89a33420c193abc-bluzidnv',
    ];

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @throws Exception
     */
    public function testDriver(): void
    {
        $stream = self::createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(File::getContent($this->path));

        $ret = self::createMock(Result::class);
        $ret->method('get')->willReturn($stream);

        $client = self::getMockBuilder(S3Client::class);
        $client = $client
            ->disableOriginalConstructor()
            ->addMethods(['deleteObject', 'putObject', 'getObject'])
            ->getMock();
        $client->method('deleteObject');
        $client
            ->expects(self::exactly(2))
            ->method('getObject')
            ->willReturnOnConsecutiveCalls($ret, new PhpUnitException(new S3Exception('', new Command('a'))));
        $client->method('putObject');

        /** @var string $bucket */
        $bucket = self::getContainer()->getParameter('aws_bucket');

        $driver = new S3Driver(
            self::getContainer()->get('doctrine_mongodb.odm.default_document_manager'),
            self::getContainer()->get('hbpf.path_generator.hash'),
            $client,
            $bucket,
        );

        $uploadedFile = new UploadedFile($this->path, '');
        $file         = $driver->save(File::getContent((string) $uploadedFile->getRealPath()));

        $file->getSize();
        self::assertEquals(File::getContent($this->path), $driver->get($file->getUrl()));

        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $driver->delete($file->getUrl());
        $driver->get($file->getUrl());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver::save
     *
     * @throws Exception
     */
    public function testDriverErr(): void
    {
        $client = self::getMockBuilder(S3Client::class);
        $client = $client
            ->disableOriginalConstructor()
            ->addMethods(['putObject'])
            ->getMock();
        $client->method('putObject');

        /** @var string $bucket */
        $bucket = self::getContainer()->getParameter('aws_bucket');

        $driver = new S3Driver(
            self::getContainer()->get('doctrine_mongodb.odm.default_document_manager'),
            self::getContainer()->get('hbpf.path_generator.hash'),
            $client,
            $bucket,
        );

        $uploadedFile = new UploadedFile($this->path, '');
        $this->setProperty($driver, 'client', new S3Client(self::FAKE_AWS_CONNECTION_ARGS));

        self::expectException(FileStorageException::class);
        $driver->save(File::getContent((string) $uploadedFile->getRealPath()));
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->path = sprintf('%s/data/Attachment.jpeg', __DIR__);
    }

}
