<?php
namespace BadaBoom\Tests\ChainNode;

use BadaBoom\ChainNode\CallbackChainNode;
use BadaBoom\Context;

/**
 * @author Vadim Tyukov <brainreflex@gmail.com>
 * @since 4/8/12
 */
class CallbackChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\CallbackChainNode');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     *
     * @dataProvider provideValidCallbacks
     */
    public function shouldBeConstructedWithCallback($callback)
    {
        new CallbackChainNode($callback);
    }

    /**
     * @test
     */
    public function couldBeConstructedWithFlagForProcessingNextNode()
    {
        new CallbackChainNode(function(){}, $shouldHandleNextNode = false);

        new CallbackChainNode(function(){}, $shouldHandleNextNode = true);
    }

    /**
     * @test
     *
     * @dataProvider provideNoCallableItems
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid callable provided
     */
    public function throwWhenConstructedWithInvalidCallback($invalidCallback)
    {
        new CallbackChainNode($invalidCallback);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToCallback()
    {
        $context = new Context(new \Exception);

        $callback = $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
        $callback
            ->expects($this->once())
            ->method('handle')
            ->with($context)
         ;

        $callbackNode = new CallbackChainNode(array($callback, 'handle'));
        $callbackNode->handle($context);
    }

    /**
     * @test
     */
    public function shouldGiveControlToNextNode()
    {
        $context = new Context(new \Exception);

        $nextNode = $this->createChainNodeMock();

        $node = new CallbackChainNode(function() {}, $shouldHandleNextNode = true);
        $node->nextNode($nextNode);

        $nextNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $node->handle($context);
    }

    /**
     * @test
     */
    public function shouldNotGiveControlToNextNode()
    {
        $nextNode = $this->createChainNodeMock();
        $nextNode
            ->expects($this->never())
            ->method('handle')
        ;

        $node = new CallbackChainNode(function() {}, $shouldHandleNextNode = false);

        $node->nextNode($nextNode);

        $node->handle(new Context(new \Exception));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    /**
     * @return array
     */
    public static function provideValidCallbacks()
    {
        $staticMethod = array(__NAMESPACE__ . '\StubMethodCall', 'staticMethod');
        $objectMethod = array(new StubMethodCall(), 'objectMethod');
        $closure = function(){};
        $function = 'is_callable';

        return array(
            array($staticMethod),
            array($objectMethod),
            array($closure),
            array($function),
        );
    }

    /**
     * @return array
     */
    public static function provideNoCallableItems()
    {
        return array(
            array('string'),
            array(1),
            array(12.2),
            array(array()),
            array(false),
            array(null),
            array(new \stdClass()),
            array(array(new \stdClass(), 'no_exist_method')),
            array(array('stdClass', 'no_exist_method')),
        );
    }
}

/**
 * Helper
 */
class StubMethodCall
{
    public static function staticMethod(){}

    public function objectMethod(){}
}
