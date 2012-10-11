<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\Context;

class SessionProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'session')
    {
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        $context->setVar(
            $this->sectionName, 
            isset($_SESSION) ? $_SESSION : array()
        );

        $this->handleNextNode($context);
    }
}