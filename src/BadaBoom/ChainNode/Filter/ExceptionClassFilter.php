<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionClassFilter extends AbstractFilter
{
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var bool
     */
    protected $defaultRule = true;

    /**
     * @return void
     */
    public function allowAll()
    {
        $this->defaultRule = true;
    }

    /**
     * @return void
     */
    public function denyAll()
    {
        $this->defaultRule = false;
    }

    /**
     * @param string $exceptionClass
     *
     * @throws InvalidArgumentException if not a exception sub class
     *
     * @return void
     */
    public function allow($exceptionClass)
    {
        $this->setRule($exceptionClass, true);
    }

    /**
     * @param string $exceptionClass
     *
     * @throws InvalidArgumentException if not a exception sub class
     *
     * @return void
     */
    public function deny($exceptionClass)
    {
        $this->setRule($exceptionClass, false);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldContinue(\Exception $exception, DataHolderInterface $data)
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

        return $this->defaultRule;
    }

    /**
     * @param string $exceptionClass
     * @param boolean $rule
     *
     * @throws \InvalidArgumentException
     * @throws \InvalidArgumentException
     *
     * @return void
     */
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