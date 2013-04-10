<?php
namespace BadaBoom\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

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
    public function handle(Context $context)
    {
        $this->client->captureException($context->getException());

        $this->handleNextNode($context);
    }
}