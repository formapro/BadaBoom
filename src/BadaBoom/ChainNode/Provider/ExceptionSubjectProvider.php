<?php

namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionSubjectProvider extends AbstractProvider
{
    /**
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     * @return mixed    
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $rc = new \ReflectionClass($exception);

        $data->set('subject', sprintf('%s: %s', $rc->getShortName(), $exception->getMessage()));

        return $this->handleNextNode($exception, $data);
    }
}
