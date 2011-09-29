<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\AbstractChainNode;

abstract class AbstractFilterChainNode extends AbstractChainNode
{
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        if ($this->filter($exception, $data)) {
            $this->handleNextNode($exception, $data);
        }
    }

    abstract public function filter(\Exception $exception, DataHolderInterface $data);
}