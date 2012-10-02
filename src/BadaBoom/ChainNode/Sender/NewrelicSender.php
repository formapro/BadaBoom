<?php
namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/2/12
 */
class NewrelicSender extends AbstractChainNode 
{
    public function __construct()
    {
        if (false == extension_loaded('newrelic')) {
            throw new \RuntimeException('The newrelic php extension is not installed. The instruction could be found at https://newrelic.com/docs/php/quick-installation-instructions-advanced-users');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        newrelic_notice_error($exception->getMessage(), $exception);
    }
}