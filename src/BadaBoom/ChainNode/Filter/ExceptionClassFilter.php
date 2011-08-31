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

    public function filter(DataHolderInterface $data)
    {
        $e = $data->get('exception');
        foreach (\array_reverse($this->rules) as $exceptionClass => $rule) {
            if ($e instanceof $exceptionClass) {
                return $rule;
            }
        }

        return false;
    }
}