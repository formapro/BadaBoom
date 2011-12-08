<?php

namespace BadaBoom;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\DataHolder\DataHolder;

class Callback
{
    /**
     *
     * @var ChainNodeInterface
     */
    protected $chain;

    /**
     *
     * @param ChainNodeInterface $chain
     */
    public function __construct(ChainNodeInterface $chain)
    {
        $this->chain = $chain;
    }

    /**
     *
     * @param \Exception $exception
     */
    public function __invoke(\Exception $exception)
    {
        $data = new DataHolder();

        $this->chain->handle($exception, $data);
    }
}