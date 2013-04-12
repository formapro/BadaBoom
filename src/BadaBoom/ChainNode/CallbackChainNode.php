<?php

namespace BadaBoom\ChainNode;

use BadaBoom\Context;

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
    protected $shouldHandleNextNode;

    /**
     * @param \Closure $callback
     * @param boolean $shouldHandleNextNode
     */
    public function __construct($callback, $shouldHandleNextNode = true)
    {
        if (false == is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callable provided');
        }

        $this->callback = $callback;
        $this->shouldHandleNextNode = $shouldHandleNextNode;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        call_user_func_array($this->callback, array($context));

        if ($this->shouldHandleNextNode) {
            $this->handleNextNode($context);
        }
    }
}