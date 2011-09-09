<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

class ErrorLevelFilter extends AbstractFilterChainNode
{
    public function handle(DataHolderInterface $data)
    {
        return $data->get('exception') instanceof \ErrorException ?
            parent::handle($data) :
            $this->handleNextNode($data);
    }

    public function filter(\Exception $e)
    {
        return true;
    }
}