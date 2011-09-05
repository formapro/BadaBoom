<?php

namespace BadaBoom\Adapter\Cache;

class ArrayCache implements CacheAdapterInterface
{
    protected $data;

    /**
     *
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->contains($id) ? $this->data[$id]['data'] : null;
    }
    
    /**
     *
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return
            isset($this->data[$id]) &&
            time() < $this->data[$id]['lifeTime'];
    }

    /**
     *
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = array('data' => $data, 'lifeTime' => time() + $lifeTime);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function delete($id)
    {
        unset($this->data);
    }
}