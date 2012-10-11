<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\Context;

class ExceptionStackTraceProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'stacktrace')
    {
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        $context->setVar($this->sectionName, (string) $context->getException());

        $this->handleNextNode($context);
    }
}