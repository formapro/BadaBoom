<?php
namespace BadaBoom\Bridge\Psr;

use BadaBoom\ChainNode\ChainNodeInterface;

use BadaBoom\Context;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{
    /**
     * @var ChainNodeInterface[]
     */
    protected $chains = array();

    /**
     * @param ChainNodeInterface $chain
     */
    public function registerChain(ChainNodeInterface $chain)
    {
        $this->chains[] = $chain;
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @throws InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        if (false == defined('Psr\Log\LogLevel::'.strtoupper($level))) {
            throw new InvalidArgumentException(sprintf('Unknown level %s was given.', $level));
        }

        $vars = array(
            'level' => constant('Psr\Log\LogLevel::'.strtoupper($level)),
            'message' => $this->prepareMessage($message, $context),
            'data' => $context,
        );

        if ($message instanceof \Exception) {
            $e = $message;
            $vars['message'] = $e->getMessage();
        } elseif (isset($context['exception']) && $context['exception'] instanceof \Exception) {
            $e = $context['exception'];
        } else {
            $e = new \Exception($vars['message']);
        }

        $chainNodeContext = new Context($e);
        $chainNodeContext->setVar('log', $vars);

        foreach ($this->chains as $chain) {
            $chain->handle($chainNodeContext);
        }
    }

    /**
     * @param string|object $message
     * @param array $vars
     *
     * @return string
     */
    private function prepareMessage($message, array $vars = array())
    {
        if (is_string($message)) {
            return $this->replaceVariables($message, $vars);
        }

        if (false == is_object($message)) {
            return 'Message of unexpected type given! '.gettype($message).' instead of string.';
        }

        if (method_exists($message, '__toString')) {
            return $this->replaceVariables((string) $message, $vars);
        }

        return 'Message of unexpected type given! '.get_class($message).' does not have __toString() method.';
    }

    /**
     * @param string $text
     * @param array $vars
     *
     * @return string
     */
    private function replaceVariables($text, array $vars = array())
    {
        $replace = array();

        foreach ($vars as $key => $val) {
            if (false == preg_match('/[^a-zA-Z0-9_\.]/', $key) && is_scalar($val)) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($text, $replace);
    }
}