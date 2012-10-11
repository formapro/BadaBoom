<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\Context;

class ExceptionSubjectProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'subject')
    {
        $this->sectionName = $sectionName;
    }
    
    /**
     * {@inheritdoc}    
     */
    public function handle(Context $context)
    {
        $rc = new \ReflectionClass($context->getException());

        $message = $context->getException()->getMessage();
        if (strlen($message) > 76) {
            $message = substr($message, 0, 76) .' ...';
        }

        $context->setVar($this->sectionName, sprintf('%s: %s', $rc->getShortName(), $message));

        return $this->handleNextNode($context);
    }
}