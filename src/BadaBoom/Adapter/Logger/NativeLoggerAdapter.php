<?php

namespace BadaBoom\Adapter\Logger;

class NativeLoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @var string
     */
    protected $destination;

    public function __construct($destination = null)
    {
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function log($content, $status)
    {
        error_log($content, $this->getDestinationType(), $this->destination);
    }

    /**
     * @return int
     */
    protected function getDestinationType()
    {
        if (empty($this->destination)) {
            return 0;
        }

        if ($this->isMail()) {
            return 1;
        }

        if ($this->isFile()) {
            return 3;
        }

        return 0;
    }

    /**
     * @return boolean
     */
    protected function isMail()
    {
        return preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $this->destination) ? true : false;
    }

    /**
     * @return boolean
     */
    protected function isFile()
    {
        if (is_file($this->destination)) {
            return is_writable($this->destination) ? true : false;
        }

        if (false == is_dir($this->destination) && is_writable(dirname($this->destination))) {
            return true;
        }

        return false;
    }
}