<?php

namespace BadaBoom\Tests\Adapter\Cache;

use BadaBoom\Adapter\Cache\ArrayCacheAdapter;

class ArrayCacheAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldImplementCacheAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Cache\ArrayCacheAdapter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\Adapter\Cache\CacheAdapterInterface'));
    }

    /**
     * @test
     */
    public function shouldAllowToSaveDataToCache()
    {
        $cache = new ArrayCacheAdapter;
        $cache->save('foo-id', 'bar-data', 23);
    }

    /**
     * @test
     */
    public function shouldAllowToCheckWhetherCacheContainsData()
    {
        $cache = new ArrayCacheAdapter;
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
        $cache = new ArrayCacheAdapter;
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
        $cache = new ArrayCacheAdapter;
        $cache->save('foo-id', 'bar-data', 2000);

        $this->assertEquals('bar-data', $cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldReturnNullIfDataNotExistInCache()
    {
        $cache = new ArrayCacheAdapter;

        $this->assertNull($cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToDeleteCachedData()
    {
        $cache = new ArrayCacheAdapter;

        $cache->save('foo-id', 'bar-data', 2000);
        $cache->save('ololo', 'ololo', 2000);

        $this->assertTrue($cache->contains('foo-id'));
        $this->assertTrue($cache->contains('ololo'));

        $cache->delete('foo-id');

        $this->assertFalse($cache->contains('foo-id'));
        $this->assertTrue($cache->contains('ololo'));
    }
}