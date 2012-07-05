<?php

namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class ServerProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     *
     * @param string $sectionName
     */
    public function __construct($sectionName = 'server')
    {
        $this->sectionName = $sectionName;
    }

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, $_SERVER);

        $this->handleNextNode($exception, $data);
    }
}