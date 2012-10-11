<?php
namespace BadaBoom\ChainNode;

use BadaBoom\Context;

class ChainNodeCollection implements ChainNodeCollectionInterface
{
    /**
     * @var array
     */
    protected $nodes = array();

    /**
     * @var ChainNodeInterface
     */
    protected $nextNode;

    /**
     * {@inheritdoc}
     */
    public function append(ChainNodeInterface $node)
    {
        array_push($this->nodes, $node);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ChainNodeInterface $node)
    {
        array_unshift($this->nodes, $node);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        if ($chain = $this->buildChain()) {
            $chain->handle($context);
        }
    }

    /**
     * @return ChainNodeInterface
     */
    protected function buildChain()
    {
        $nodes = $this->nodes;

        if ($this->nextNode) {
            $nodes[] = $this->nextNode;
        }

        $previousNode = null;
        foreach($nodes as $node) {
            if ($previousNode) {
                $previousNode->nextNode($node);
            }

            $previousNode = $node;
        }

        return array_shift($nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function nextNode(ChainNodeInterface $node)
    {
        $this->nextNode = $node;
    }
}