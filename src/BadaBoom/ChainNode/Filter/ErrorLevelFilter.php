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

    public function handle(DataHolderInterface $data)
    {
        return $data->get('exception') instanceof \ErrorException ?
            parent::handle($data) :
            $this->handleNextNode($data);
    }

    public function filter(\Exception $e)
    {
        foreach ($this->denyErrors as $error) {
            if ($error == $e->getSeverity()) {
                return false;
            }
        }

        return true;
    }
}