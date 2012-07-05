<?php

namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class EnvironmentProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     *
     * @param string $sectionName
     */
    public function __construct($sectionName = 'env')
    {
        $this->sectionName = $sectionName;
    }

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, $_ENV);

        $this->handleNextNode($exception, $data);
    }
}