<?php

namespace BadaBoom\Tests\Adapter\Cache;

use BadaBoom\Adapter\Cache\FileCacheAdapter;

class FileCacheAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $cacheFile;

    public function setUp()
    {
        $this->cacheFile = tempnam(sys_get_temp_dir(), 'badaboom');
    }

    /**
     *
     * @test
     */
    public function shouldImplementCacheAdapterInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Adapter\Cache\FileCacheAdapter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\Adapter\Cache\CacheAdapterInterface'));
    }

    /**
     *
     * @test
     */
    public function shouldRequireCacheFilePathToBeProvidedInConstructor()
    {
        new FileCacheAdapter($this->cacheFile);
    }

    /**
     * @test
     */
    public function shouldAllowToSaveDataToCache()
    {
        $cache = new FileCacheAdapter($this->cacheFile);
        $cache->save('foo-id', 'bar-data', 23);
    }

    /**
     * @test
     */
    public function shouldAllowToCheckWhetherCacheContainsData()
    {
        $cache = new FileCacheAdapter($this->cacheFile);
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
        $cache = new FileCacheAdapter($this->cacheFile);
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
        $cache = new FileCacheAdapter($this->cacheFile);
        $cache->save('foo-id', 'bar-data', 2000);

        $this->assertEquals('bar-data', $cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldReturnNullIfDataNotExistInCache()
    {
        $cache = new FileCacheAdapter($this->cacheFile);

        $this->assertNull($cache->fetch('foo-id'));
    }

    /**
     *
     * @test
     */
    public function shouldAllowToDeleteCachedData()
    {
        $cache = new FileCacheAdapter($this->cacheFile);

        $cache->save('foo-id', 'bar-data', 2000);
        $cache->save('ololo', 'ololo', 2000);

        $this->assertTrue($cache->contains('foo-id'));
        $this->assertTrue($cache->contains('ololo'));

        $cache->delete('foo-id');

        $this->assertFalse($cache->contains('foo-id'));
        $this->assertTrue($cache->contains('ololo'));
    }

    /**
     * @test
     */
    public function shouldSyncChangesBetweenInstances()
    {
        $cacheOne = new FileCacheAdapter($this->cacheFile);
        $cacheTwo = new FileCacheAdapter($this->cacheFile);

        $cacheOne->save('foo-id', 'bar-data', 2000);

        $this->assertTrue($cacheTwo->contains('foo-id'));
    }
}