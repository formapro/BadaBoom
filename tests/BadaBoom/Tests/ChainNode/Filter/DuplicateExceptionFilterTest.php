<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\ChainNode\Filter\DuplicateExceptionFilter;
use BadaBoom\Context;

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
        new DuplicateExceptionFilter($this->createCacheAdapterMock(), $lifetime = 2000);
    }

    /**
     * @test
     */
    public function shouldGenerateCacheIdForException()
    {
        $filter = new DuplicateExceptionFilter($this->createCacheAdapterMock(), $lifetime = 2000);

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
        $filter = new DuplicateExceptionFilter($this->createCacheAdapterMock(), $lifetime = 2000);

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
        $context = new Context($exception);

        $cache = $this->createCacheAdapterMock();
        $filter = new DuplicateExceptionFilter($cache, $lifetime = 2000);

        $cacheId = $filter->generateCacheId($exception);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(true))
        ;

        $this->assertFalse($filter->shouldContinue($context));
    }

    /**
     * @test
     */
    public function shouldAllowPropagationIfExceptionNotInCache()
    {
        $exception = new \Exception('foo');
        $context = new Context($exception);
        
        $cache = $this->createCacheAdapterMock();
        $filter = new DuplicateExceptionFilter($cache, $lifetime = 2000);

        $cacheId = $filter->generateCacheId($exception);
        $cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(false))
        ;

        $this->assertTrue($filter->shouldContinue($context));
    }

    /**
     * @test
     */
    public function shouldAddExceptionToCacheIfNotInCache()
    {
        $expectedLifeTime = 2000;

        $exception = new \Exception('foo');
        $context = new Context($exception);
        
        $cache = $this->createCacheAdapterMock();

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

        $filter->shouldContinue($context);
    }

    protected function createCacheAdapterMock()
    {
        return $this->getMock('BadaBoom\Adapter\Cache\CacheAdapterInterface');
    }
}