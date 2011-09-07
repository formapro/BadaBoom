<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\Adapter\Cache\CacheAdapterInterface;

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

    public function __construct(CacheAdapterInterface $cache, $lifeTime = 360)
    {
        $this->cache = $cache;
        $this->lifeTime = $lifeTime;
    }

    public function filter(\Exception $exception)
    {
        $id = md5(
            get_class($exception) .
            $exception->getFile() .
            $exception->getLine());
//var_dump($id);
        if ($this->cache->contains($id)) {
//            var_dump(1);
            return false;
        }
//var_dump(2);
        $this->cache->save($id, 1, $this->lifeTime);
        return true;
    }
}