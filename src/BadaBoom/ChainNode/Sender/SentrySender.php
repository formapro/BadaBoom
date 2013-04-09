<?php
namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

class SentrySender extends AbstractChainNode
{
    /**
     * @var \Raven_Client
     */
    protected $client;

    /**
     * @param \Raven_Client $client
     */
    public function __construct(\Raven_Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $this->client->captureException($exception);

        $this->handleNextNode($exception, $data);
    }
}