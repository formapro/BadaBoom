<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionClassFilter extends AbstractFilterChainNode
{
    protected $rules = array();

    public function allow($exceptionClass)
    {
        $this->setRule($exceptionClass, true);
    }

    public function deny($exceptionClass)
    {
        $this->setRule($exceptionClass, false);
    }

    protected function setRule($exceptionClass, $rule)
    {
        if (false == \class_exists($exceptionClass)) {
            throw new \InvalidArgumentException('Class not exists: `'.$exceptionClass.'`');
        }

        $rc = new \ReflectionClass($exceptionClass);
        if ('Exception' != $exceptionClass  && false == $rc->isSubclassOf('Exception')) {
            throw new \InvalidArgumentException('Class `'.$exceptionClass.'` is not a subclass of `Exception`');
        }

        $this->rules[$exceptionClass] = $rule;
    }

    public function filter(\Exception $e)
    {
        $caughtExceptionClass = get_class($e);
        while ($caughtExceptionClass) {
            foreach ($this->rules as $exceptionClass => $rule) {
                if ($caughtExceptionClass === $exceptionClass) {
                    return $rule;
                }
            }

            $rc = new \ReflectionClass($caughtExceptionClass);
            if ($prc = $rc->getParentClass()) {
                $caughtExceptionClass = $prc->getName();
            } else {
                $caughtExceptionClass = null;
            }
        }

        return false;
    }
}