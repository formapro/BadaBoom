<?php

namespace BadaBoom\Tests\ChainNode;

class ChainNodeCollectionInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldExtendChainNodeInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\ChainNodeCollectionInterface');
        $this->assertTrue($rc->implementsInterface('BadaBoom\ChainNode\ChainNodeInterface'));
    }
}
