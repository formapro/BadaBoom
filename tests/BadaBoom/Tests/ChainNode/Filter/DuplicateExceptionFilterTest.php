<?php

namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\Adapter\Cache\ArrayCacheAdapter;
use BadaBoom\ChainNode\Filter\DuplicateExceptionFilter;

class DuplicateExceptionFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\DuplicateExceptionFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilterChainNode'));
    }

    /**
     * 
     * @test
     */
    public function shouldRequireCacheAdapterAndLifeTimeProvidedInConstructor()
    {
        new DuplicateExceptionFilter(new ArrayCacheAdapter, 2000);
    }

    /**
     * 
     * @test
     */
    public function shouldPassIfExceptionComesFirstTime()
    {
        $filter = new DuplicateExceptionFilter(new ArrayCacheAdapter, 2000);

        $this->assertTrue($filter->filter(new \Exception('foo')));
    }

    /**
     *
     * @test
     */
    public function shouldFilterIfExceptionComesForSecondTime()
    {
        $filter = new DuplicateExceptionFilter(new ArrayCacheAdapter, 2000);

        $e = new \Exception('foo');

        $this->assertTrue($filter->filter($e));
        $this->assertFalse($filter->filter($e));
    }

    /**
     *
     * @test
     */
    public function shouldPassIfLifeTimeIsOver()
    {
        $filter = new DuplicateExceptionFilter(new ArrayCacheAdapter, 1);

        $e = new \Exception('foo');

        $this->assertTrue($filter->filter($e));
        $this->assertFalse($filter->filter($e));

        sleep(1);

        $this->assertTrue($filter->filter($e));
    }
}