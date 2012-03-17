<?php

namespace BadaBoom\ChainNode;

interface ChainNodeCollectionInterface extends ChainNodeInterface
{
    /**
     * @param ChainNodeInterface $node
     *
     * @return void
     */
    function append(ChainNodeInterface $node);

    /**
     * @param ChainNodeInterface $node
     *
     * @return void
     */
    function prepend(ChainNodeInterface $node);
}
