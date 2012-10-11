<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\Context;

class ServerProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     *
     * @param string $sectionName
     */
    public function __construct($sectionName = 'server')
    {
        $this->sectionName = $sectionName;
    }

    public function handle(Context $context)
    {
        $context->setVar($this->sectionName, $_SERVER);

        $this->handleNextNode($context);
    }
}