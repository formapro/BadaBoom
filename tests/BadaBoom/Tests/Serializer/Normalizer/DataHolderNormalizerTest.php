<?php

namespace BadaBoom\Tests\Serializer\Normalizer;

class DataHolderNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Normalizer\DataHolderNormalizer');
        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Normalizer\NormalizerInterface'));
    }
}