<?php

namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionSummaryProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'summary')
    {
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, array(
            'class' => get_class($exception),
            'uri' => $this->getUri(),
            'code' => $this->getCode($exception),
            'message' => $exception->getMessage(),
            'file' => "{$exception->getFile()}, Line: {$exception->getLine()}",
        ));

        $this->handleNextNode($exception, $data);
    }

    /**
     * @return string
     */
    protected function getUri()
    {
        $uri = 'undefined';
        if (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            $uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } elseif(isset($_SERVER['argv'])) {
            $uri = implode(' ', $_SERVER['argv']);
        }

        return $uri;
    }

    /**
     * @param \Exception $exception
     *
     * @return int|string
     */
    protected function getCode(\Exception $exception)
    {
        return $exception instanceof \ErrorException ?
            $this->getHumanErrorCode($exception->getSeverity()) :
            $exception->getCode()
        ;
    }

    /**
     *
     * @param string $name
     *
     * @return int|string
     */
    public function getHumanErrorCode($errorCode)
    {
        $errorCodes = array(
            'E_ERROR' => E_ERROR,
            'E_RECOVERABLE_ERROR' => E_RECOVERABLE_ERROR,
            'E_WARNING' => E_WARNING,
            'E_PARSE' => E_PARSE,
            'E_NOTICE' => E_NOTICE,
            'E_STRICT' => E_STRICT,
            'E_CORE_ERROR' => E_CORE_ERROR,
            'E_CORE_WARNING' => E_CORE_WARNING,
            'E_COMPILE_ERROR' => E_COMPILE_ERROR,
            'E_COMPILE_WARNING' => E_COMPILE_WARNING,
            'E_USER_ERROR' => E_USER_ERROR,
            'E_USER_WARNING' => E_USER_WARNING,
            'E_USER_NOTICE' => E_USER_NOTICE,
            'E_ALL' => E_ALL,
        );

        $humanErrorCode = array_search($errorCode, $errorCodes);

        return false !== $humanErrorCode ?
            $humanErrorCode :
            'E_UNKNOWN'
        ;
    }
}