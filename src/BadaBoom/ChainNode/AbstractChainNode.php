<?php
namespace BadaBoom\ChainNode;

use BadaBoom\Context;

abstract class AbstractChainNode implements ChainNodeInterface
{
    /**
     *
     * @var ChainNodeInterface
     */
    protected $nextNode;

    /**
     * @param Context $context
     *
     * @return void
     */
    public function handleNextNode(Context $context)
    {
        if ($this->nextNode) {
            $this->nextNode->handle($context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function nextNode(ChainNodeInterface $node)
    {
        return $this->nextNode = $node;
    }
}