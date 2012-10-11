<?php
namespace BadaBoom\ChainNode;

use BadaBoom\Context;

interface ChainNodeInterface
{
    /**
     * @param Context
     *
     * @return void
     */
    function handle(Context $context);

    /**
     * @param ChainNodeInterface $node
     *
     * @return ChainNodeInterface
     */
    function nextNode(ChainNodeInterface $node);
}