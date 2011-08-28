<?php

namespace BadaBoom\ChainNode\Sender;

interface SenderAdapterInterface
{
    function send($destination, $data);
}