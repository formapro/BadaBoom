<?php
namespace BadaBoom\ChainNode\Decorator;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\ChainNode\ChainNodeInterface;

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
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        try {
            $this->chainNode->handle($exception, $data);
        } catch (\Exception $internalException) {
            $chainExceptions = $data->get('chain_exceptions', array());
            $chainExceptions[] = array(
                'chain' => get_class($this->chainNode),
                'class' => get_class($internalException),
                'message' => $internalException->getMessage(),
                'code' => $internalException->getCode(),
                'line' => $internalException->getLine(),
                'file' => $internalException->getFile(),
                'has_previous' => $internalException->getPrevious() instanceof \Exception,
            );

            $data->set('chain_exceptions', $chainExceptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function nextNode(ChainNodeInterface $node = null)
    {
        return $this->chainNode->nextNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function push(ChainNodeInterface $pushedNode)
    {
        return $this->chainNode->push($pushedNode);
    }
}