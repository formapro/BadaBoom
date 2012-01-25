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
    public function shouldCorrectlyEncodeScalarsInSubArrays()
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
     * @test
     */
    public function shouldTrimStringInSubArrays()
    {
        $data = array(
            'subArray' => array(
                'str1' => "\nsome str\t",
                'str2' => "\t\nsome str",
            ),
        );

        $expectedText = "SubArray\n\tStr1: some str\n\tStr2: some str\n\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * @test
     */
    public function shouldCorrectlyEncodeNotArraySection()
    {
        $data = array(
            'foo' => 1,
            'bar' => 'bar'
        );

        $expectedText = "Foo\n\t1\nBar\n\tbar\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * @test
     */
    public function shouldCorrectlyEncodeNotArrayStringWithEndLinesSpecialChars()
    {
        $data = array(
            'str' => "foo\nbar",
        );

        $expectedText = "Str\n\tfoo\n\tbar\n";

        $encoder = new TextEncoder;

        $this->assertEquals($expectedText, $encoder->encode($data, null));
    }

    /**
     * @test
     */
    public function shouldTrimString()
    {
        $data = array(
            'str' => "\nfoo\t",
        );

        $expectedText = "Str\n\tfoo\n";

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