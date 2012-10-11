<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\Context;

class ErrorLevelFilter extends AbstractFilter
{
    /**
     * @var array
     */
    protected $denyErrors = array();

    /**
     * @param int $error
     */
    public function deny($error)
    {
        $this->denyErrors[$error] = $error;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldContinue(Context $context)
    {
        if (false == $context->getException() instanceof \ErrorException) {
            return true;
        }

        foreach ($this->denyErrors as $error) {
            if ($error == $context->getException()->getSeverity()) {
                return false;
            }
        }

        return true;
    }
}