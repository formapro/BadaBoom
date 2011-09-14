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
            array('foo',            'Should not support normalization of strings'),
            array(array(),          'Should not support normalization of arrays'),
            array(123,              'Should not support normalization of numbers'),
        );
    }

    public static function provideNotSupportedDenormalizations()
    {
        return array(
            array(array('foo'), 'stdClass'),
            array(array('foo'), 'BadaBoom\DataHolder\DataHolder'),
            array('foo',        'BadaBoom\DataHolder\DataHolder'),
            array(123,          'BadaBoom\DataHolder\DataHolder'),
        );
    }

    public static function provideNormalizationData()
    {
        $noArrayDataHolder = new DataHolder();
        $noArrayDataHolder->set('foo', 123);
        $noArrayDataHolder->set('bar', 'bar');
        $noArrayDataHolder->set('bar2', 2.3);
        $noArrayDataHolder->set('foo2', new \stdClass());
        $noArrayDataHolder->set('exception', new \Exception('err'));
        $expectedNoArray = array();

        $arrayDataHolder = new DataHolder();
        $arrayDataHolder->set('foo', array());
        $expectedArray = array('foo' => array());

        $arrayWithScalarsDataHolder = new DataHolder();
        $arrayWithScalarsDataHolder->set('foo', array('foo' => 'foo', 'bar' => 123));
        $expectedArrayWithScalars = array('foo' => array('foo' => 'foo', 'bar' => 123));

        $arrayWithObjectDataHolder = new DataHolder();
        $arrayWithObjectDataHolder->set('foo', array('obj' => new \stdClass()));
        $expectedArrayWithObject = array('foo' => array('obj' => var_export(new \stdClass(), true)));

        $arrayWithSubArrayDataHolder = new DataHolder();
        $arrayWithSubArrayDataHolder->set('foo', array('sub' => array('a' => 'b')));
        $expectedArrayWithSubArray = array('foo' => array('sub' => var_export(array('a' => 'b'), true)));


        return array(
            array(new DataHolder(), array(), 'An empty DataHolder should be converted to an empty array'),
            array($noArrayDataHolder, $expectedNoArray, 'Should ignore any no array values'),
            array($arrayDataHolder, $expectedArray, 'Should normalize array values'),
            array($arrayWithScalarsDataHolder, $expectedArrayWithScalars, 'Should do nothing with simple scalar types'),
            array($arrayWithObjectDataHolder, $expectedArrayWithObject, 'Should do var export on all objects in the array'),
            array($arrayWithSubArrayDataHolder, $expectedArrayWithSubArray, 'Should do var export on all sub arrays in the array'),
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
    public function shouldNotSupportAnyDenormalizations($data, $type)
    {
        $normalizer = new DataHolderNormalizer;

        $this->assertFalse($normalizer->supportsDenormalization($data, $type), 'Should not support any kinds of denormalization');
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
    public function shouldThrowOnDenormalizeCall($data, $type)
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
