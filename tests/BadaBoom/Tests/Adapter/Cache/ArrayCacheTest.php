<?php

namespace BadaBoom\Tests\Adapter\Cache;

use BadaBoom\Adapter\Cache\ArrayCache;

class ArrayCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementCacheAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Cache\ArrayCache');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\Adapter\Cache\CacheAdapterInterface'));
    }

    /**
     * @test
     */
    public function shouldAllowToSaveDataToCache()
    {
        $cache = new ArrayCache;
        $cache->save('foo-id', 'bar-data', 23);
    }

    /**
     * @test
     */
    public function shouldAllowToCheckWhetherCacheContainsData()
    {
        $cache = new ArrayCache;
        $cache->save('foo-id', 'bar-data', 2000);

        $this->assertTrue($cache->contains('foo-id'));
        $this->assertFalse($cache->contains('bar-id'));
    }

    /**
     *
     * @test
     */
    public function shouldNotContainsDataIfItIsExpired()
    {
        $cache = new ArrayCache;
        $cache->save('foo-id', 'bar-data', 2);

        $this->assertTrue($cache->contains('foo-id'));
        sleep(2);
        $this->assertFalse($cache->contains('foo-id'));
    }

    /**
     * 
     * @test
     */
    public function shouldAllowToFetchCachedData()
    {
        $cache = new ArrayCache;
        $cache->save('foo-id', 'bar-data', 2000);

        $this->assertEquals('bar-data', $cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldReturnNullIfDataNotExistInCache()
    {
        $cache = new ArrayCache;

        $this->assertNull($cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToDeleteCachedData()
    {
        $cache = new ArrayCache;

        $cache->save('foo-id', 'bar-data', 2000);

        $this->assertTrue($cache->contains('foo-id'));

        $cache->delete('foo-id');

        $this->assertFalse($cache->contains('foo-id'));
    }
}