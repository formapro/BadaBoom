<?php

namespace BadaBoom\Tests\Serializer\Normalizer;

use BadaBoom\Serializer\Normalizer\DataHolderNormalizer;
use BadaBoom\DataHolder\DataHolder;

class DataHolderNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public static function provideSupportedNormalizations()
    {
        return array(
            array(new DataHolder(), 'Should support normalization of DataHolder to all formats'),
        );
    }

    public static function provideNotSupportedNormalizations()
    {
        return array(
            array(new \stdClass(),  'Should not support normalization of not DataHolder'),
            array('foo',            'Should not support normalization of not DataHolder'),
            array(array(),          'Should not support normalization of not DataHolder'),
            array(123,              'Should not support normalization of not DataHolder'),
        );
    }

    public static function provideNotSupportedDenormalizations()
    {
        return array(
            array(array('foo'), 'stdClass',                       'Should not support any types of denormalization'),
            array(array('foo'), 'BadaBoom\DataHolder\DataHolder', 'Should not support any types of denormalization'),
            array('foo',        'BadaBoom\DataHolder\DataHolder', 'Should not support any types of denormalization'),
            array(123,          'BadaBoom\DataHolder\DataHolder', 'Should not support any types of denormalization'),
        );
    }

    public static function provideNormalizationData()
    {
        $scalarDataHolder = new DataHolder();
        $scalarDataHolder->set('foo', 123);
        $scalarDataHolder->set('bar', 'bar');
        $scalarDataHolder->set('ololo', 2.3);
        $scalarArray = array('foo' => 123, 'bar' => 'bar', 'ololo' => 2.3);

        return array(
            array(new DataHolder(), array(), 'An empty DataHolder should be converted to an empty array'),
            array($scalarDataHolder, $scalarArray, 'Scalar DataHolder should be converted to scalar array'),
        );
    }

    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Normalizer\DataHolderNormalizer');
        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Normalizer\NormalizerInterface'));
    }

    /**
     *
     * @test
     *
     * @dataProvider provideSupportedNormalizations
     */
    public function shouldSupportNormalizationOf($data, $failMessage)
    {
        $normalizer = new DataHolderNormalizer;

        $this->assertTrue($normalizer->supportsNormalization($data), $failMessage);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideNotSupportedNormalizations
     */
    public function shouldNotSupportNormalizationOf($data, $failMessage)
    {
        $normalizer = new DataHolderNormalizer;

        $this->assertFalse($normalizer->supportsNormalization($data), $failMessage);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideNotSupportedDenormalizations
     */
    public function shouldNotSupportAnyDenormalizations($data, $type, $failMessage)
    {
        $normalizer = new DataHolderNormalizer;

        $this->assertFalse($normalizer->supportsDenormalization($data, $type), $failMessage);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideNotSupportedDenormalizations
     *
     * @expectedException Symfony\Component\Serializer\Exception\UnsupportedException
     * @expectedExceptionMessage Denormalization is not supported by this normalizer
     */
    public function shouldThrowOnDenormalizeCall($data, $type, $failMessage)
    {
        $normalizer = new DataHolderNormalizer;

        $normalizer->denormalize($data, $type);
    }

    /**
     * @test
     *
     * @dataProvider provideNormalizationData
     */
    public function shouldNormalizeDataHolderIntoArray($data, $expectedNormalizedData, $failMessage)
    {
        $normalizer = new DataHolderNormalizer;

        $actualNormalizedData = $normalizer->normalize($data);

        $this->assertEquals($expectedNormalizedData, $actualNormalizedData, $failMessage);
    }
}