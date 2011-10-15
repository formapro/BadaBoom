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
    function nextNode(ChainNodeInterface $node);
}