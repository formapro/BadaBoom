<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\AbstractChainNode;

abstract class AbstractFilterChainNode extends AbstractChainNode
{
    public function handle(DataHolderInterface $data)
    {
        if ($data->get('exception') instanceof \Exception) {
            if ($this->filter($data)) {
                $this->handleNextNode($data);
            }
        }
    }

    abstract public function filter(DataHolderInterface $data);
}