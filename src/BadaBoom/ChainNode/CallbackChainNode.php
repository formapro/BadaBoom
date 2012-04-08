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
  protected  $callback;

  /**
   * @param Callable|Closure $callback
   */
  public function __construct($callback)
  {
    if (false == is_callable($callback)) {
      throw new \InvalidArgumentException('Invalid callable provided');
    }

    $this->callback = $callback;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(\Exception $exception, DataHolderInterface $data)
  {
    if (true === call_user_func_array($this->callback, array($exception, $data))) {
      $this->handleNextNode($exception, $data);
    }
  }
}