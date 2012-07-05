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

        $message = $exception->getMessage();
        if (strlen($message) > 76) {
            $message = substr($message, 0, 76) .' ...';
        }

        $data->set('subject', sprintf('%s: %s', $rc->getShortName(), $message));

        return $this->handleNextNode($exception, $data);
    }
}
