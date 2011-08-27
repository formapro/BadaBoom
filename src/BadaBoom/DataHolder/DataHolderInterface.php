<?php

namespace BadaBoom\DataHolder;

/**
 *
 * @package    BadaBoom
 * 
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
interface DataHolderInterface extends \IteratorAggregate
{
    /**
     *
     * @param string $name
     * @param mixed
     *
     * @return void
     */
    public function set($name, $value);

    /**
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);
}