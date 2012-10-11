<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

abstract class AbstractFilter extends AbstractChainNode
{
    /**
     * {@inheritdoc}
     */
    public final function handle(Context $context)
    {
        if ($this->shouldContinue($context)) {
            $this->handleNextNode($context);
        }
    }

    /**
     * @abstract
     *
     * @param Context $context
     *
     * @return boolean
     */
    abstract public function shouldContinue(Context $context);
}