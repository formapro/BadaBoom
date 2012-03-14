<?php
namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionStackTraceProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @param string $sectionName
     */
    public function __construct($sectionName = 'stacktrace')
    {
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, (string) $exception);

        $this->handleNextNode($exception, $data);
    }
}