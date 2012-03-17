<?php

namespace BadaBoom\ChainNode;

use BadaBoom\DataHolder\DataHolderInterface;

abstract class AbstractChainNode implements ChainNodeInterface
{
    /**
     *
     * @var ChainNodeInterface
     */
    protected $nextNode;

    /**
     *
     * @param DataHolderInterface $data
     *
     * @return void
     */
    public function handleNextNode(\Exception $exception, DataHolderInterface $data)
    {
        if ($this->nextNode) {
            $this->nextNode->handle($exception, $data);
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function nextNode(ChainNodeInterface $node = null)
    {
        return $this->nextNode = $node;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function push(ChainNodeInterface $pushedNode)
    {
        if ($currentNextNode = $this->nextNode) {
            $pushedNode->nextNode($currentNextNode);
        }

        return $this->nextNode($pushedNode);
    }
}