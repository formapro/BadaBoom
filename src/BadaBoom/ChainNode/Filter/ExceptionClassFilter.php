<?php

namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionClassFilter extends AbstractFilter
{
    protected $rules = array();

    public function filter(\Exception $exception, DataHolderInterface $data)
    {
        $caughtExceptionClass = get_class($exception);
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
}