<?php

namespace BadaBoom\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolderInterface;

class ExceptionSubjectProvider extends AbstractProvider
{
    /**
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     * @return mixed    
     */
    public function handle(DataHolderInterface $data)
    {
        $e = $data->get('exception');
        if(false == empty($e)) {
            $data->set('subject', sprintf('%s: %s', get_class($e), $e->getMessage()));
        }

        return $this->handleNextNode($data);
    }
}
