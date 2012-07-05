<?php

namespace BadaBoom\DataHolder;

/**
 *
 * @package    BadaBoom
 *
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class DataHolder implements DataHolderInterface
{
    /**
     *
     * @var array
     */
    protected $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function has($name)
    {
        return \array_key_exists($name, $this->data);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}