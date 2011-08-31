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
     */
    public function shouldAllowPassDestinationToConstructor()
    {
        new NativeLoggerAdapter('sdfasdf');
    }

    /**
     * @test
     */
    public function shouldLogThroughSystemLoggerIfDestinationWasNotSet()
    {
        $log = 'Log!';

        $logger = new NativeLoggerAdapter();

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 0, null)
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

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 1, $mail)
        ;

        $logger = new NativeLoggerAdapter($mail);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogToExistingWritableFile()
    {
        $file = '/log.txt';
        $log  = 'log me';

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_file')
            ->expects($this->once())
            ->method('is_file')
            ->with($file)
            ->will($this->returnValue(true))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_writable')
            ->expects($this->once())
            ->method('is_writable')
            ->with($file)
            ->will($this->returnValue(true))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 3, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogViaSystemLoggerIfGivenFileIsNotWritable()
    {
        $file = '/log.txt';
        $log  = 'log me';

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_file')
            ->expects($this->once())
            ->method('is_file')
            ->with($file)
            ->will($this->returnValue(true))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_writable')
            ->expects($this->once())
            ->method('is_writable')
            ->with($file)
            ->will($this->returnValue(false))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 0, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogToNonexistentFileThatIsInWritableDir()
    {
        $file = '/path/file.txt';
        $dirname = '/path';
        $log  = 'log me';

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_dir')
            ->expects($this->once())
            ->method('is_dir')
            ->with($file)
            ->will($this->returnValue(false))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'dirname')
            ->expects($this->once())
            ->method('dirname')
            ->with($file)
            ->will($this->returnValue($dirname))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_writable')
            ->expects($this->once())
            ->method('is_writable')
            ->with($dirname)
            ->will($this->returnValue(true))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 3, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogViaSystemLoggerIfDestinationIsNonexistentFileThatIsInNotWritableDir()
    {
        $file = '/path/file.txt';
        $dirname = '/path';
        $log  = 'log me';

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_dir')
            ->expects($this->once())
            ->method('is_dir')
            ->with($file)
            ->will($this->returnValue(false))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'dirname')
            ->expects($this->once())
            ->method('dirname')
            ->with($file)
            ->will($this->returnValue($dirname))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_writable')
            ->expects($this->once())
            ->method('is_writable')
            ->with($dirname)
            ->will($this->returnValue(false))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 0, $file)
        ;

        $logger = new NativeLoggerAdapter($file);
        $logger->log($log, 'status');
    }

    /**
     * @test
     */
    public function shouldLogViaSystemLoggerIfGivenDestinationIsUnsolved()
    {
        $log = 'Log me';
        $destination = 'some unsolved destination...';

        // Safe mode: to be sure that checks will be completed
        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'is_writable')
            ->expects($this->once())
            ->method('is_writable')
            ->will($this->returnValue(false))
        ;

        $this->fumocker->getMock('BadaBoom\Adapter\Logger', 'error_log')
            ->expects($this->once())
            ->method('error_log')
            ->with($log, 0, $destination)
        ;

        $logger = new NativeLoggerAdapter($destination);
        $logger->log($log, 'status');
    }
}