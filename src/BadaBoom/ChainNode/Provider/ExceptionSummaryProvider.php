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
     *
     * @param string $sectionName
     */
    public function __construct($sectionName = 'summary')
    {
        $this->sectionName = $sectionName;
    }

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, array(
            'class' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => "{$exception->getFile()}, Line: {$exception->getLine()}",
        ));

        $this->handleNextNode($exception, $data);
    }
}