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
    public function nextNode(ChainNodeInterface $node)
    {
        return $this->nextNode = $node;
    }
}