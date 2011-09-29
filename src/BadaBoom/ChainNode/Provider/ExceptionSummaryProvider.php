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

    /**
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     *
     * @return void
     */
    public function handle(DataHolderInterface $data)
    {
        $e = $data->get('exception');
        if ($e instanceof \Exception) {
            $data->set($this->sectionName, array(
                'class' => get_class($e),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => "{$e->getFile()}, Line: {$e->getLine()}",
            ));
        }

        $this->handleNextNode($data);
    }
}