<?php

namespace BadaBoom\DataHolder;

/**
 *
 * @package    BadaBoom
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
interface DataHolderInterface extends \IteratorAggregate
{
    /**
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function set($name, $value);

    /**
     * @param $name
     * @param null|mixed $default
     *
     * @return void
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function has($name);
}