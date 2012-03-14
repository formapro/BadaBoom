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

    /**
     * @test
     */
    public function shouldNormalizeEmtpyDataHolderAsEmptyArray()
    {
        $data = new DataHolder();

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(array(), $normalizer->normalize($data));
    }

    /**
     * @test
     */
    public function shouldLeaveScalarsValuesAsIs()
    {
        $data = new DataHolder();
        $data->set('int', 555);
        $data->set('str', 'bar');
        $data->set('flt', 1.11);

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'int' => 555,
                'str' => 'bar',
                'flt' => 1.11
            ),
            $normalizer->normalize($data)
        );
    }

    /**
     * @test
     */
    public function shouldLeaveEmptySubArrayAsIs()
    {
        $data = new DataHolder();
        $data->set('subArray', array());

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(),
            ),
            $normalizer->normalize($data)
        );
    }

    /**
     * @test
     */
    public function shouldVarExportObject()
    {
        $data = new DataHolder();
        $data->set('obj', new \stdClass());

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'obj' => var_export(new \stdClass(), true),
            ),
            $normalizer->normalize($data)
        );
    }

    /**
     * @test
     */
    public function shouldLeaveScalarInSubArrayAsIs()
    {
        $data = new DataHolder();
        $data->set('subArray', array(
            'int' => 555,
            'str' => 'bar',
            'flt' => 1.11
        ));

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'int' => 555,
                    'str' => 'bar',
                    'flt' => 1.11
                ),
            ),
            $normalizer->normalize($data)
        );
    }

    /**
     * @test
     */
    public function shouldVarExporyArraysInSubArrays()
    {
        $data = new DataHolder();
        $data->set('subArray', array(
            'subSubArray' => array('foo' => 'foo'),
        ));

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'subSubArray' => var_export(array('foo' => 'foo'), true),
                ),
            ),
            $normalizer->normalize($data)
        );
    }

    /**
     * @test
     */
    public function shouldVarExporyObjectsInSubArrays()
    {
        $data = new DataHolder();
        $data->set('subArray', array(
            'obj' => new \stdClass(),
        ));

        $normalizer = new DataHolderNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'obj' => var_export(new \stdClass(), true),
                ),
            ),
            $normalizer->normalize($data)
        );
    }

    /**
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
     * @test
     *
     * @dataProvider provideNotSupportedNormalizations
     *
     * @expectedException Symfony\Component\Serializer\Exception\UnsupportedException
     */
    public function throwIfTryToNormalizeNotSupportedData($notSupportedData)
    {
        $normalizer = new DataHolderNormalizer;

        $normalizer->normalize($notSupportedData);
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
     * @expectedExceptionMessage Denormalization of any formats is not supported.
     */
    public function throwAlwaysOnDenormalizeCall($data, $type)
    {
        $normalizer = new DataHolderNormalizer;

        $normalizer->denormalize($data, $type);
    }
}