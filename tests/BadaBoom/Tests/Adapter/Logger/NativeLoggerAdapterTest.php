<?php

namespace BadaBoom\Tests\Adapter\Logger;

use BadaBoom\Adapter\Logger\NativeLoggerAdapter;
use Fumocker\Fumocker;

class NativeLoggerAdapterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Fumocker\Fumocker
     */
    protected $fumocker;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->fumocker = new Fumocker();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->fumocker->cleanup();
    }

    /**
     * @test
     */
    public function shouldImplementLoggerAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Logger\NativeLoggerAdapter');
        $this->assertTrue($rc->implementsInterface('BadaBoom\Adapter\Logger\LoggerAdapterInterface'));
    }

    /**
     * @test
     */
    public function shouldConstructWithoutArguments()
    {
        new NativeLoggerAdapter();
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The destination type can not be resolved for destination
     */
    public function throwIfDestinationHasUnsolvedType()
    {
        new NativeLoggerAdapter('sdfasdf');
    }

    /**
     * @test
     */
    public function shouldLogThroughSystemLoggerIfDestinationWasNotSet()
    {
        $log = 'Log!';
        $expectedDestinationType = 0;

        $logger = new NativeLoggerAdapter();

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, $expectedDestinationType, null)
        ;

        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogToMail()
    {
        $mail   = 'my@mail.com';
        $log    = 'log me';
        $expectedDestinationType  = 1;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, $expectedDestinationType, $mail)
        ;

        $logger = new NativeLoggerAdapter($mail);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogToExistingWritableFile()
    {
        $file = $this->createTempFile();
        $log  = 'log me';
        $expectedDestinationType  = 3;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, $expectedDestinationType, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');

        unlink($file);
    }

    /**
     * @test
     */
    public function throwIfGivenDestinationFileIsNotWritable()
    {
        $file = $this->createTempFile();
        chmod($file, 000);

        try {
            new NativeLoggerAdapter($file);
            $this->fail('Expected exception was not thrown.');
        } catch (\InvalidArgumentException $e) {
            $this->assertContains("The destination file '{$file}' is not writable", $e->getMessage());
        }

        unlink($file);
    }

    /**
     * @test
     */
    public function shouldLogToNonexistentFileThatIsInWritableDir()
    {
        $dir = $this->createTempDir();
        $file = $dir . DIRECTORY_SEPARATOR . 'log.txt';
        $log  = 'log me';
        $expectedDestinationType = 3;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, $expectedDestinationType, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');
        
        rmdir($dir);
    }

    /**
     * @test
     */
    public function throwIfDestinationIsNonexistentFileThatIsInNotWritableDir()
    {
        $dir = $this->createTempDir();
        chmod($dir, '000');
        
        $file = $dir . DIRECTORY_SEPARATOR . 'log.txt';

        try {
            new NativeLoggerAdapter($file);
            $this->fail('Expected exception was not thrown.');
        } catch (\InvalidArgumentException $e) {
            $this->assertContains('The destination type can not be resolved for destination', $e->getMessage());
        }

        rmdir($dir);

    }

    /**
     * @return string
     */
    protected function createTempFile()
    {
        return tempnam(sys_get_temp_dir() . DIRECTORY_SEPARATOR, uniqid());
    }

    /**
     * @return string
     */
    protected function createTempDir()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        mkdir($dir);
        return $dir;
    }
}