<?php

namespace BadaBoom\ChainNode;

use BadaBoom\DataHolder\DataHolderInterface;

class EmptyChainNode extends AbstractChainNode
{
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $this->handleNextNode($exception, $data);
    }
}