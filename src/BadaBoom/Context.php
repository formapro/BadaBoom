<?php
namespace BadaBoom;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 10/11/12
 */
class Context 
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var array
     */
    protected $vars;
    
    public function __construct(\Exception $exception, array $vars = array()) 
    {
        $this->exception = $exception;
        $this->vars = $vars;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param string $name
     * @param mixed $value
     * 
     * @return void
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @param string $name
     * @param null $default
     * 
     * @return mixed
     */
    public function getVar($name, $default = null)
    {
        return array_key_exists($name, $this->vars) ? $this->vars[$name] : $default;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param string $name
     * 
     * @return bool
     */
    public function hasVar($name)
    {
        return \array_key_exists($name, $this->vars);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->vars);
    }
}