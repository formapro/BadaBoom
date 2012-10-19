<?php
namespace BadaBoom\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\Sender\NewrelicSender;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/2/12
 */
class NewrelicSenderTest extends \PHPUnit_Framework_TestCase 
{
    protected $fumocker;
    
    protected function setUp()
    {
        $this->fumocker = new \Fumocker\Fumocker();

        $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'newrelic_set_appname');
        $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'newrelic_notice_error');
        $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'function_exists');
    }
    
    protected function tearDown()
    {
        $this->fumocker->cleanup();
    }
    
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Sender\NewrelicSender');
        
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }
    
    /**
     * @test
     * 
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The newrelic php extension is not installed.
     */
    public function throwIfNewrelicExtensionIsNotLoaded()
    {
        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $mock->expects($this->once())
            ->method('extension_loaded')
            ->with('newrelic')
            ->will($this->returnValue(false))
        ;
        
        new NewrelicSender();
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArgumentsIfNewrelicExtensionIsLoaded()
    {
        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $mock->expects($this->once())
            ->method('extension_loaded')
            ->with('newrelic')
            ->will($this->returnValue(true))
        ;
        
        new NewrelicSender();
    }

    /**
     * @test
     */
    public function shouldCallNewrelicNoticeErrorFunction()
    {
        $expectedException = new \LogicException($expectedMessage = 'An error! Alarm!!!');
        
        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $mock->expects($this->once())
            ->method('extension_loaded')
            ->with('newrelic')
            ->will($this->returnValue(true))
        ;

        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'newrelic_notice_error');
        $mock->expects($this->once())
            ->method('newrelic_notice_error')
            ->with($expectedMessage, $expectedException)
        ;

        $sender = new NewrelicSender();
        
        $sender->handle(new Context($expectedException));
    }

    /**
     * @test
     */
    public function shouldCallNewrelicSetApplicationNameIfSetInOptions()
    {
        $expectedApplicationName = 'badaboom project';

        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $mock->expects($this->once())
            ->method('extension_loaded')
            ->with('newrelic')
            ->will($this->returnValue(true))
        ;

        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'function_exists');
        $mock->expects($this->once())
            ->method('function_exists')
            ->with('newrelic_set_appname')
            ->will($this->returnValue(true))
        ;

        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'newrelic_set_appname');
        $mock->expects($this->once())
            ->method('newrelic_set_appname')
            ->with($expectedApplicationName)
        ;

        $sender = new NewrelicSender(array(
            'application_name' => $expectedApplicationName
        ));

        $sender->handle(new Context(new \Exception));
    }

    /**
     *
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $mock = $this->fumocker->getMock('BadaBoom\ChainNode\Sender', 'extension_loaded');
        $mock->expects($this->once())
            ->method('extension_loaded')
            ->with('newrelic')
            ->will($this->returnValue(true))
        ;
        
        $context = new Context(new \Exception('foo'));

        $nextChainNode = $this->createMockChainNode();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sender = new NewrelicSender();
        $sender->nextNode($nextChainNode);

        $sender->handle($context);
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}