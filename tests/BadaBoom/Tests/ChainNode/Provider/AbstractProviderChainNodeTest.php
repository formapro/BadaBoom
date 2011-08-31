<?php

namespace BadaBoom\Tests\ChainNode\Provider;

class AbstractChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldNotBeInstantiable()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\AbstractProviderChainNode');
        $this->assertFalse($rc->isInstantiable());
    }
}