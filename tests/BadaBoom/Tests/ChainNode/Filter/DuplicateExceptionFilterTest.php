<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\Adapter\Cache\ArrayCacheAdapter;
use BadaBoom\ChainNode\Filter\DuplicateExceptionFilter;
use BadaBoom\DataHolder\DataHolder;

class DuplicateExceptionFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractFilter()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\DuplicateExceptionFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilter'));
    }

    /**
     * @test
     */
    public function shouldRequireCacheAdapterAndLifeTimeProvidedInConstructor()
    {
        new DuplicateExceptionFilter($this->createCacheAdapter(), $lifetime = 2000);
    }

    /**
     * @test
     */
    public function shouldGenerateCacheIdForException()
    {
        $filter = new DuplicateExceptionFilter($this->createCacheAdapter(), $lifetime = 2000);

        $id = $filter->generateCacheId(new \Exception('foo'));

        $this->assertInternalType('string', $id);
        $this->assertEquals(32, strlen($id));
    }

    /**
     * @test
     */
    public function shouldGenerateSameCacheIdAsExpected()
    {
        $exception = new \Exception('foo');
        $filter = new DuplicateExceptionFilter($this->createCacheAdapter(), $lifetime = 2000);

        $expectedCacheId = md5(get_class($exception) . $exception->getFile() . $exception->getLine());
        $actualCacheId = $filter->generateCacheId($exception);

        $this->assertEquals($expectedCacheId, $actualCacheId);
    }

    /**
     * @test
     */
    public function shouldDenyPropagationIfExceptionAlreadyInCache()
    {
        $exception = new \Exception('foo');

        $cache = $this->createCacheAdapter();
        $filter = new DuplicateExceptionFilter($cache, $lifetime = 2000);

        $cacheId = $filter->generateCacheId($exception);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(true))
        ;

        $this->assertFalse($filter->shouldContinue($exception, new DataHolder));
    }

    /**
     * @test
     */
    public function shouldAllowPropagationIfExceptionNotInCache()
    {
        $exception = new \Exception('foo');
        $cache = $this->createCacheAdapter();
        $filter = new DuplicateExceptionFilter($cache, $lifetime = 2000);

        $cacheId = $filter->generateCacheId($exception);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(false))
        ;

        $this->assertTrue($filter->shouldContinue($exception, new DataHolder));
    }

    /**
     * @test
     */
    public function shouldAddExceptionToCacheIfNotInCache()
    {
        $expectedLifeTime = 2000;

        $exception = new \Exception('foo');
        $cache = $this->createCacheAdapter();

        $filter = new DuplicateExceptionFilter($cache, $expectedLifeTime);

        $cacheId = $filter->generateCacheId($exception);

        $cache
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($cacheId),
                $this->equalTo(1),
                $this->equalTo($expectedLifeTime)
            )
        ;

        $cache
            ->expects($this->any())
            ->method('contains')
            ->will($this->returnValue(false))
        ;

        $filter->shouldContinue($exception, new DataHolder);
    }

    protected function createCacheAdapter()
    {
        return $this->getMock('BadaBoom\Adapter\Cache\CacheAdapterInterface');
    }
}