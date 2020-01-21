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
use Psr\Http\Message\StreamInterface;
use ReflectionException;
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
        $stream->method('getContents')->willReturn(file_get_contents($this->path));

        $ret = self::createMock(Result::class);
        $ret->method('get')->willReturn($stream);

        $client = self::getMockBuilder(S3Client::class);
        $client = $client
            ->disableOriginalConstructor()
            ->addMethods(['deleteObject', 'putObject', 'getObject'])
            ->getMock();
        $client->method('deleteObject');
        $client->expects(self::at(1))->method('getObject')->willReturn($ret);
        $client
            ->expects(self::at(3))
            ->method('getObject')
            ->willThrowException(new S3Exception('', new Command('a')));
        $client->method('putObject');

        $driver = new S3Driver(
            self::$container->get('doctrine_mongodb.odm.default_document_manager'),
            self::$container->get('hbpf.path_generator.hash'),
            $client,
            self::$container->getParameter('aws_bucket')
        );

        $uploadedFile = new UploadedFile($this->path, '');
        $file         = $driver->save((string) file_get_contents((string) $uploadedFile->getRealPath()));

        $file->getSize();
        self::assertEquals(file_get_contents($this->path), $driver->get($file->getUrl()));

        self::expectException(FileStorageException::class);
        self::expectExceptionCode(FileStorageException::FILE_NOT_FOUND);
        $driver->delete($file->getUrl());
        $driver->get($file->getUrl());
    }

    /**
     * @covers \Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3\S3Driver::save
     *
     * @throws FileStorageException
     * @throws ReflectionException
     */
    public function testDriverErr(): void
    {
        $client = self::getMockBuilder(S3Client::class);
        $client = $client
            ->disableOriginalConstructor()
            ->addMethods(['putObject'])
            ->getMock();
        $client->method('putObject');
        $driver = new S3Driver(
            self::$container->get('doctrine_mongodb.odm.default_document_manager'),
            self::$container->get('hbpf.path_generator.hash'),
            $client,
            self::$container->getParameter('aws_bucket')
        );

        $uploadedFile = new UploadedFile($this->path, '');
        $this->setProperty($driver, 'client', new S3Client(self::FAKE_AWS_CONNECTION_ARGS));

        self::expectException(FileStorageException::class);
        $driver->save((string) file_get_contents((string) $uploadedFile->getRealPath()));
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
