<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

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
    public function shouldContinue(\Exception $exception, DataHolderInterface $data)
    {
        if (false == $exception instanceof \ErrorException) {
            return true;
        }

        foreach ($this->denyErrors as $error) {
            if ($error == $exception->getSeverity()) {
                return false;
            }
        }

        return true;
    }
}