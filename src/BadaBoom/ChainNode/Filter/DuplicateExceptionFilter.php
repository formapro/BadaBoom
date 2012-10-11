<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\Adapter\Cache\CacheAdapterInterface;
use BadaBoom\Context;

class DuplicateExceptionFilter extends AbstractFilter
{
    /**
     * @var CacheAdapterInterface
     */
    protected $cache;

    /**
     * @var integer
     */
    protected $lifeTime;

    /**
     * @param CacheAdapterInterface $cache
     * @param int $lifeTime
     */
    public function __construct(CacheAdapterInterface $cache, $lifeTime = 360)
    {
        $this->cache = $cache;
        $this->lifeTime = $lifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldContinue(Context $context)
    {
        $cacheId = $this->generateCacheId($context->getException());
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