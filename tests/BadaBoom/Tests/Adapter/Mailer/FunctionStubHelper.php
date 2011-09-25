<?php

namespace BadaBoom\Adapter\Mailer;

/**
 * This file contains the stubs of functions that are used into defined namespace
 */
use BadaBoom\Tests\FunctionCallbackRegistry;

function mail()
{
    return FunctionCallbackRegistry::getInstance()->call('mail', func_get_args());
}