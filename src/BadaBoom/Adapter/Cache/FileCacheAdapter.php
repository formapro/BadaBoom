<?php

namespace BadaBoom\Adapter\Cache;

class FileCacheAdapter extends ArrayCacheAdapter
{
    protected $file;

    public function __construct($file)
    {
        touch($file);
        $this->file = $file;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        $this->refresh();

        return parent::fetch($id);
    }

    protected function refresh()
    {
        if ($data = unserialize(file_get_contents($this->file))) {
            $this->data = $data;
        }
    }

    protected function dump()
    {
        file_put_contents($this->file, serialize($this->data));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function contains($id)
    {
        $this->refresh();

        return parent::contains($id);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        parent::save($id, $data, $lifeTime);
        
        $this->dump();
    }

    /**
     *
     * {@inheritdoc}
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->dump();
    }
}