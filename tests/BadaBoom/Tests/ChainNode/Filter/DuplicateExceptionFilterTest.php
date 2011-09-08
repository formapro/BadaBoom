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
        $cache = $this->createCacheAdapter();

        new DuplicateExceptionFilter($cache, 2000);
    }

    /**
     *
     * @test
     */
    public function shouldGenerateCacheIdForException()
    {
        $filter = new DuplicateExceptionFilter($this->createCacheAdapter(), 2000);

        $id = $filter->generateCacheId(new \Exception('foo'));

        $this->assertInternalType('string', $id);
        $this->assertEquals(32, strlen($id));
    }

    /**
     * 
     * @test
     */
    public function shouldGenerateSameCacheIdAsExpected()
    {
        $e = new \Exception('foo');
        $filter = new DuplicateExceptionFilter($this->createCacheAdapter(), 2000);

        $expectedCacheId = md5(get_class($e) . $e->getFile() . $e->getLine());
        $actualCacheId = $filter->generateCacheId($e);

        $this->assertEquals($expectedCacheId, $actualCacheId);
    }

    /**
     * 
     * @test
     */
    public function shouldFilterIfExceptionAlreadyInCache()
    {
        $e = new \Exception('foo');
        $cache = $this->createCacheAdapter();
        $filter = new DuplicateExceptionFilter($cache, 2000);

        $cacheId = $filter->generateCacheId($e);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(true));


        $this->assertFalse($filter->filter($e));
    }

    /**
     * 
     * @test
     */
    public function shouldPassIfExceptionNotInCache()
    {
        $e = new \Exception('foo');
        $cache = $this->createCacheAdapter();
        $filter = new DuplicateExceptionFilter($cache, 2000);

        $cacheId = $filter->generateCacheId($e);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(false));

        $this->assertTrue($filter->filter($e));
    }

    /**
     *
     * @test
     */
    public function shouldSaveExceptionToTheCacheIfNotInCache()
    {
        $expectedLifeTime = 2000;

        $e = new \Exception('foo');
        $cache = $this->createCacheAdapter();

        $filter = new DuplicateExceptionFilter($cache, $expectedLifeTime);

        $cacheId = $filter->generateCacheId($e);

        $cache
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($cacheId), $this->equalTo(1), $this->equalTo($expectedLifeTime));
        $cache
            ->expects($this->any())
            ->method('contains')
            ->will($this->returnValue(false));

        $this->assertTrue($filter->filter($e));
    }

    protected function createCacheAdapter()
    {
        return $this->getMock('BadaBoom\Adapter\Cache\CacheAdapterInterface');
    }
}