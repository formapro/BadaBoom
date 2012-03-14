<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolder;

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
        $filter = $this->createMockFilter();

        $filter->shouldContinue(new \Exception, new DataHolder);
    }

    /**
     * @test
     */
    public function shouldContinuePropagationPassExceptionAndDataToNextNodeIfShouldContinueReturnTrue()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->createMockChainNode();
        $nextNode
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($exception),
                $this->equalTo($data)
            )
        ;

        $filter = $this->createMockFilter();
        $filter
                ->expects($this->atLeastOnce())
                ->method('shouldContinue')
                ->will($this->returnValue(true))
        ;
        $filter->nextNode($nextNode);

        $filter->handle($exception, $data);
    }

    /**
     * @test
     */
    public function shouldNotContinuePropagationIfShouldContinueReturnFalse()
    {
        $exception = new \Exception('foo');
        $data = new DataHolder();

        $nextNode = $this->createMockChainNode();
        $nextNode
                ->expects($this->never())
                ->method('handle')
        ;

        $filter = $this->createMockFilter();
        $filter
                ->expects($this->atLeastOnce())
                ->method('shouldContinue')
                ->will($this->returnValue(false))
        ;
        $filter->nextNode($nextNode);

        $filter->handle($exception, $data);
    }

    protected function createMockFilter()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\Filter\AbstractFilter');
    }

    protected function createMockChainNode()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}