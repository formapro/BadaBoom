<?php

namespace BadaBoom\Adapter\Logger;

class NativeLoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @var string
     */
    protected $destination;

    /**
     * @var int
     */
    protected $destinationType;

    /**
     * @param string|null $destination
     */
    public function __construct($destination = null)
    {
        $this->destinationType = $this->guessDestinationType($destination);
        $this->destination     = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function log($content, $level)
    {
        error_log($content, $this->destinationType, $this->destination);
    }

    /**
     * @return string
     */
    protected function guessDestinationType($destination)
    {
        if (empty($destination)) {
            return 0;
        }

        if ($this->isMail($destination)) {
            return 1;
        }

        if ($this->isFile($destination)) {
            return 3;
        }

        throw new \InvalidArgumentException(sprintf(
            'The destination type can not be resolved for destination %s',
            var_export($destination, true)
        ));
    }

    /**
     * @return boolean
     */
    protected function isMail($destination)
    {
        return preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $destination) ? true : false;
    }

    /**
     * @return boolean
     */
    protected function isFile($destination)
    {
        $file = new \SplFileInfo($destination);

        if ($file->isFile()) {
            if ($file->isWritable()) {
                return true;
            }

            throw new \InvalidArgumentException(sprintf(
                'The destination file %s is not writable',
                var_export($destination, true)
            ));
        }


        $dir = $file->getPath();
        if (false == $file->isDir() && is_dir($dir)) {
            if(is_writable($dir)) {
                return true;
            }

            throw new \InvalidArgumentException(sprintf(
                'The destination directory %s is not writable. The log file %s cannot be created',
                var_export($dir, true),
                var_export($file->getBasename(), true)
            ));
        }

        return false;
    }
}