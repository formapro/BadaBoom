<?php

namespace BadaBoom\Tests;

/**
 * TODO refactor the usage of this functionality after the Fumocker release.
 */
class FunctionCallbackRegistry
{
    /**
     * @var \BadaBoom\Tests\FunctionCallbackRegistry
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $callbacks = array();

    private function __construct(){}

    /**
     * @return FunctionCallbackRegistry
     */
    public static function getInstance()
    {
        if (true == empty(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @param string $name
     * @param Callable $callback
     * @return void
     */
    public function registerCallback($name, $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function call($name, array $arguments)
    {
        return call_user_func_array($this->callbacks[$name], $arguments);
    }
}