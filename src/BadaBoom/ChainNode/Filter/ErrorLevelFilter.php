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

    public function filter(\Exception $e, DataHolderInterface $data)
    {
        if (false == $e instanceof \ErrorException) {
            return true;
        }

        foreach ($this->denyErrors as $error) {
            if ($error == $e->getSeverity()) {
                return false;
            }
        }

        return true;
    }
}