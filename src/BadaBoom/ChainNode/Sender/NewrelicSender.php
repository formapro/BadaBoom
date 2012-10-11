<?php
namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/2/12
 */
class NewrelicSender extends AbstractChainNode 
{
    public function __construct($applicationName = null)
    {
        if (false == extension_loaded('newrelic')) {
            throw new \RuntimeException('The newrelic php extension is not installed. The instruction could be found at https://newrelic.com/docs/php/quick-installation-instructions-advanced-users');
        }
        
        if (function_exists('newrelic_set_appname') && $applicationName) { 
            newrelic_set_appname($applicationName);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        newrelic_notice_error(
            $context->getException()->getMessage(), 
            $context->getException()
        );
        
        $this->handleNextNode($context);
    }
}