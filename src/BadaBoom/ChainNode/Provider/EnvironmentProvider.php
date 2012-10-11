<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\Context;

class EnvironmentProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     *
     * @param string $sectionName
     */
    public function __construct($sectionName = 'env')
    {
        $this->sectionName = $sectionName;
    }

    public function handle(Context $context)
    {
        $context->setVar($this->sectionName, $_ENV);

        $this->handleNextNode($context);
    }
}