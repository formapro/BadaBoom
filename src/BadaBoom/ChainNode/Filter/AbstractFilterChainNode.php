<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\AbstractChainNode;

abstract class AbstractFilterChainNode extends AbstractChainNode
{
    public function handle(DataHolderInterface $data)
    {
        $e = $data->get('exception');
        if ($e instanceof \Exception) {
            if ($this->filter($e)) {
                $this->handleNextNode($data);
            }
        }
    }

    abstract public function filter(\Exception $exception);
}