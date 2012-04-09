<?php

namespace BadaBoom\ChainNode;

use BadaBoom\DataHolder\DataHolderInterface;

/**
 * @author Vadim Tyukov <brainreflex@gmail.com>
 * @since 4/8/12
 */
class CallbackChainNode extends AbstractChainNode
{
    /**
     * @var Callable|Closure
     */
    protected $callback;

    /**
     * @var boolean
     */
    protected $handleNextNode;

    /**
     * @param Callable|Closure $callback
     * @param boolean $handleNextNode
     */
    public function __construct($callback, $handleNextNode = true)
    {
        if (false == is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callable provided');
        }

        $this->callback = $callback;
        $this->handleNextNode = $handleNextNode;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        call_user_func_array($this->callback, array($exception, $data));

        if ($this->handleNextNode) {
            $this->handleNextNode($exception, $data);
        }
    }
}