<?php

namespace BadaBoom\ChainNode;

use BadaBoom\DataHolder\DataHolderInterface;

interface ChainNodeInterface
{
    /**
     *
     * @param DataHolderInterface
     *
     * @return void
     */
    function handle(\Exception $exception, DataHolderInterface $data);

    /**
     *
     * @param ChainNodeInterface $node
     *
     * @return ChainNodeInterface
     */
    function nextNode(ChainNodeInterface $node = null);

    /**
     * Pushed a node after the current node, if the current node has next node the pushed will be set among them.
     *
     * @param ChainNodeInterface $pushedNode
     *
     * @return ChainNodeInterface a pushed node
     */
    function push(ChainNodeInterface $pushedNode);
}