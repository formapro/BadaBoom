<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

class ErrorLevelFilter extends AbstractFilterChainNode
{
    protected $denyErrors = array();

    public function deny($error)
    {
        $this->denyErrors[$error] = $error;
    }

    public function filter(\Exception $exception, DataHolderInterface $data)
    {
        if (false == $exception instanceof \ErrorException) {
            return true;
        }

        foreach ($this->denyErrors as $error) {
            if ($error == $exception->getSeverity()) {
                return false;
            }
        }

        return true;
    }
}