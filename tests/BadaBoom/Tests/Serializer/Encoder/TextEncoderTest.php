<?php

namespace BadaBoom\Tests\Serializer\Encoder;

use BadaBoom\Serializer\Encoder\TextEncoder;

class TextEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Encoder\TextEncoder');
        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Encoder\EncoderInterface'));
    }

    /**
     *
     * @test
     */
    public function shouldCorrectlyEncodeEmptySections()
    {
        $data = array(
            'foo' => array(),
            'bar' => array());

        $expectedText = "Foo\n\nBar\n\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * 
     * @test
     */
    public function shouldCorrectlyEncodeScalars()
    {
        $data = array(
            'foo' => array(
                'a' => 1,
                'b' => 'some str'),
            'bar' => array(
                'c' => 2.3),
        );

        $expectedText = "Foo\n\tA: 1\n\tB: some str\n\nBar\n\tC: 2.3\n\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * 
     * @test
     */
    public function shouldCorrectlyEncodeComplexTypes()
    {
        $data = array(
            'foo' => array('a' => var_export(new \stdClass, true)),
            'bar' => array('b' => var_export(array(), true)));

        $expectedText = "Foo\n\tA: stdClass::__set_state(array(\n\t\t))\n\nBar\n\tB: array (\n\t\t)\n\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * @test
     */
    public function shouldSupportPlainTextEncoding()
    {
        $encoder = new TextEncoder();
        $this->assertTrue($encoder->supportsEncoding('plain-text'));
    }
}