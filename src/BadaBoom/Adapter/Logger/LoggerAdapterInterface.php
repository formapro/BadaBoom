<?php

namespace BadaBoom\Adapter\Logger;

use BadaBoom\Adapter\SenderAdapterInterface;

interface LoggerAdapterInterface extends SenderAdapterInterface
{
    /**
     * @param string $content
     * @param mixed $level
     */
    function log($content, $level);
}
 
