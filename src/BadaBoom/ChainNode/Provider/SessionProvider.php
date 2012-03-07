<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class SessionProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'session')
    {
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, isset($_SESSION) ? $_SESSION : array());

        $this->handleNextNode($exception, $data);
    }
}