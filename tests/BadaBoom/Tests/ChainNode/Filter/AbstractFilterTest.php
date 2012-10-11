<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\Context;

class AbstractFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldNotBeInstantiable()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\AbstractFilter');
        $this->assertFalse($rc->isInstantiable());
    }

    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractChainNode()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\AbstractFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function shouldDecideWhetherExecutionShouldContinueOrNot()
    {
        $filter = $this->createAbstractFilterMock();

        $filter->shouldContinue(new Context(new \Exception));
    }

    /**
     * @test
     */
    public function shouldContinuePropagationPassExceptionAndDataToNextNodeIfShouldContinueReturnTrue()
    {
        $context = new Context(new \Exception);

        $nextNode = $this->createChainNodeMock();
        $nextNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $filter = $this->createAbstractFilterMock();
        $filter
            ->expects($this->atLeastOnce())
            ->method('shouldContinue')
            ->will($this->returnValue(true))
        ;
        $filter->nextNode($nextNode);

        $filter->handle($context);
    }

    /**
     * @test
     */
    public function shouldNotContinuePropagationIfShouldContinueReturnFalse()
    {
        $context = new Context(new \Exception);

        $nextNode = $this->createChainNodeMock();
        $nextNode
                ->expects($this->never())
                ->method('handle')
        ;

        $filter = $this->createAbstractFilterMock();
        $filter
                ->expects($this->atLeastOnce())
                ->method('shouldContinue')
                ->will($this->returnValue(false))
        ;
        $filter->nextNode($nextNode);

        $filter->handle($context);
    }

    protected function createAbstractFilterMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\Filter\AbstractFilter');
    }

    protected function createChainNodeMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}