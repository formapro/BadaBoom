<?php
namespace BadaBoom\Tests\Serializer\Normalizer;

use BadaBoom\Context;
use BadaBoom\Serializer\Normalizer\ContextNormalizer;

class ContextNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public static function provideSupportedNormalizations()
    {
        return array(
            array(new Context(new \Exception), 'Should support normalization of Context to all formats'),
        );
    }

    public static function provideNotSupportedNormalizations()
    {
        return array(
            array(new \stdClass(),  'Should not support normalization of not Context'),
            array('foo',            'Should not support normalization of strings'),
            array(array(),          'Should not support normalization of arrays'),
            array(123,              'Should not support normalization of numbers'),
        );
    }

    public static function provideNotSupportedDenormalizations()
    {
        return array(
            array(array('foo'), 'stdClass'),
            array(array('foo'), 'BadaBoom\Context'),
            array('foo',        'BadaBoom\Context'),
            array(123,          'BadaBoom\Context'),
        );
    }

    /**
     * @test
     */
    public function shouldNormalizeEmtpyContextAsEmptyArray()
    {
        $context = new Context(new \Exception);

        $normalizer = new ContextNormalizer;

        $this->assertEquals(array(), $normalizer->normalize($context));
    }

    /**
     * @test
     */
    public function shouldLeaveScalarsValuesAsIs()
    {
        $context = new Context(new \Exception);
        $context->setVar('int', 555);
        $context->setVar('str', 'bar');
        $context->setVar('flt', 1.11);

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'int' => 555,
                'str' => 'bar',
                'flt' => 1.11
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldLeaveEmptySubArrayAsIs()
    {
        $context = new Context(new \Exception);
        $context->setVar('subArray', array());

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(),
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldVarExportObject()
    {
        $context = new Context(new \Exception);
        $context->setVar('obj', new \stdClass());

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'obj' => var_export(new \stdClass(), true),
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldLeaveScalarInSubArrayAsIs()
    {
        $context = new Context(new \Exception);
        $context->setVar('subArray', array(
            'int' => 555,
            'str' => 'bar',
            'flt' => 1.11
        ));

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'int' => 555,
                    'str' => 'bar',
                    'flt' => 1.11
                ),
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldVarExporyArraysInSubArrays()
    {
        $context = new Context(new \Exception);
        $context->setVar('subArray', array(
            'subSubArray' => array('foo' => 'foo'),
        ));

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'subSubArray' => var_export(array('foo' => 'foo'), true),
                ),
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldVarExporyObjectsInSubArrays()
    {
        $context = new Context(new \Exception);
        $context->setVar('subArray', array(
            'obj' => new \stdClass(),
        ));

        $normalizer = new ContextNormalizer;

        $this->assertEquals(
            array(
                'subArray' => array(
                    'obj' => var_export(new \stdClass(), true),
                ),
            ),
            $normalizer->normalize($context)
        );
    }

    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Normalizer\ContextNormalizer');
        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Normalizer\NormalizerInterface'));
    }

    /**
     *
     * @test
     *
     * @dataProvider provideSupportedNormalizations
     */
    public function shouldSupportNormalizationOf($context, $failMessage)
    {
        $normalizer = new ContextNormalizer;

        $this->assertTrue($normalizer->supportsNormalization($context), $failMessage);
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
        $normalizer = new ContextNormalizer;

        $normalizer->normalize($notSupportedData);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideNotSupportedNormalizations
     */
    public function shouldNotSupportNormalizationOf($context, $failMessage)
    {
        $normalizer = new ContextNormalizer;

        $this->assertFalse($normalizer->supportsNormalization($context), $failMessage);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideNotSupportedDenormalizations
     */
    public function shouldNotSupportAnyDenormalizations($context, $type)
    {
        $normalizer = new ContextNormalizer;

        $this->assertFalse($normalizer->supportsDenormalization($context, $type), 'Should not support any kinds of denormalization');
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
    public function throwAlwaysOnDenormalizeCall($context, $type)
    {
        $normalizer = new ContextNormalizer;

        $normalizer->denormalize($context, $type);
    }
}