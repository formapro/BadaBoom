<?php
namespace BadaBoom\ChainNode\Decorator;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\Context;

class SafeChainNodeDecorator implements ChainNodeInterface
{
    /**
     * @var \BadaBoom\ChainNode\ChainNodeInterface
     */
    protected $chainNode;

    /**
     * @param \BadaBoom\ChainNode\ChainNodeInterface $chainNode
     */
    public function __construct(ChainNodeInterface $chainNode)
    {
        $this->chainNode = $chainNode;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        try {
            $this->chainNode->handle($context);
        } catch (\Exception $internalException) {
            $chainExceptions = $context->getVar('chain_exceptions', array());

            $chainExceptions[] = array(
                'chain' => get_class($this->chainNode),
                'exception' => (string) $internalException 
            );

            $context->setVar('chain_exceptions', $chainExceptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function nextNode(ChainNodeInterface $node = null)
    {
        return $this->chainNode->nextNode($node);
    }
}