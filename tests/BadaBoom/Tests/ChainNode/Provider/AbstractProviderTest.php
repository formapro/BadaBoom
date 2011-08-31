<?php

namespace BadaBoom\Tests\ChainNode\Provider;

class AbstractProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldNotBeInstantiable()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\AbstractProvider');
        $this->assertFalse($rc->isInstantiable());
    }
}