<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\AbstractChainNode;

abstract class AbstractFilter extends AbstractChainNode
{
    /**
     * {@inheritdoc}
     */
    public final function handle(\Exception $exception, DataHolderInterface $data)
    {
        if ($this->shouldContinue($exception, $data)) {
            $this->handleNextNode($exception, $data);
        }
    }

    /**
     * @abstract
     *
     * @param \Exception $exception
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     */
    abstract public function shouldContinue(\Exception $exception, DataHolderInterface $data);
}