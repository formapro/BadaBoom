<?php
namespace BadaBoom\ChainNode\Filter;

use BadaBoom\Context;

/**
 * For more info {@link http://php.net/manual/en/function.php-sapi-name.php}
 */
class SapiFilter extends AbstractFilter
{
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var boolean
     */
    protected $defaultRule = true;

    /**
     * @param string $sapiName
     */
    public function allow($sapiName)
    {
        $this->rules[$sapiName] = true;
    }

    /**
     * @return void
     */
    public function allowAll()
    {
        $this->defaultRule = true;
    }

    /**
     * @param string $sapiName
     */
    public function deny($sapiName)
    {
        $this->rules[$sapiName] = false;
    }

    /**
     * @return void
     */
    public function denyAll()
    {
        $this->defaultRule = false;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldContinue(Context $context)
    {
        foreach ($this->rules as $sapiName => $rule) {
            if (php_sapi_name() === $sapiName) {
                return $rule;
            }
        }

        return $this->defaultRule;
    }
}