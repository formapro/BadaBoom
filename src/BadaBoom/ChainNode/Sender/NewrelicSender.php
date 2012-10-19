<?php
namespace BadaBoom\ChainNode\Sender;

use Symfony\Component\OptionsResolver\OptionsResolver;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/2/12
 */
class NewrelicSender extends AbstractChainNode 
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if (false == extension_loaded('newrelic')) {
            throw new \RuntimeException('The newrelic php extension is not installed. The instruction could be found at https://newrelic.com/docs/php/quick-installation-instructions-advanced-users');
        }
        
        $this->options = $this->getOptionResolver()->resolve($options);
    }

    /**
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getOptionResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setOptional(array(
            'application_name'
        ));
        
        return $resolver;
    }
    
    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        if (function_exists('newrelic_set_appname') && isset($this->options['application_name'])) {
            newrelic_set_appname($this->options['application_name']);
        }
        
        newrelic_notice_error(
            $context->getException()->getMessage(), 
            $context->getException()
        );
        
        $this->handleNextNode($context);
    }
}