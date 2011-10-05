<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\Adapter\Cache\CacheAdapterInterface;
use \BadaBoom\DataHolder\DataHolderInterface;

class DuplicateExceptionFilter extends AbstractFilterChainNode
{
    /**
     * @var BadaBoom\Adapter\Cache\CacheAdapterInterface
     */
    protected $cache;

    /**
     * @var integer
     */
    protected $lifeTime;

    /**
     * @param \BadaBoom\Adapter\Cache\CacheAdapterInterface $cache
     * @param int $lifeTime
     */
    public function __construct(CacheAdapterInterface $cache, $lifeTime = 360)
    {
        $this->cache = $cache;
        $this->lifeTime = $lifeTime;
    }

    /**
     * 
     * {@inheritdoc}
     */
    public function filter(\Exception $exception, DataHolderInterface $data)
    {
        $cacheId = $this->generateCacheId($exception);
        if ($this->cache->contains($cacheId)) {
            return false;
        }

        $this->cache->save($cacheId, 1, $this->lifeTime);
        
        return true;
    }

    /**
     * @param \Exception $exception
     * 
     * @return string
     */
    public function generateCacheId(\Exception $exception)
    {
        return md5(get_class($exception) . $exception->getFile() . $exception->getLine());
    }
}