<?php

namespace BadaBoom\DataHolder;

class ExceptionHolder extends DataHolder
{
    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getException()
    {
        return $this->exception;
    }
}