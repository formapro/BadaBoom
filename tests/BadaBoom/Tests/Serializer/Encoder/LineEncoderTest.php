<?php
namespace BadaBoom\Tests\Serializer\Encoder;

use BadaBoom\Serializer\Encoder\LineEncoder;
use BadaBoom\Context;

class LineEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementEncoderInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Encoder\LineEncoder');
        
        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Encoder\EncoderInterface'));
    }

    /**
     * @test
     */
    public function shouldImplementNormalizationAwareInterface()
    {
        $rc = new \ReflectionClass('BadaBoom\Serializer\Encoder\LineEncoder');

        $this->assertTrue($rc->implementsInterface('Symfony\Component\Serializer\Encoder\NormalizationAwareInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new LineEncoder();
    }

    /**
     * @test
     */
    public function shouldSupportLineFormat()
    {
        $encoder = new LineEncoder();
        
        $this->assertTrue($encoder->supportsEncoding('line'));
    }

    /**
     * @test
     * 
     * @expectedException \Symfony\Component\Serializer\Exception\UnsupportedException
     * @expectedExceptionMessage stdClass is not supported by this encoder. Expected instance of BadaBoom\Context
     */
    public function throwIfNotContextPassedForEncoding()
    {
        $encoder = new LineEncoder();
        
        $encoder->encode(new \stdClass(), 'line');
    }

    /**
     * @test
     */
    public function shouldUseDefaultLineFormatToEncodeContext()
    {
        $encoder = new LineEncoder();

        $result = $encoder->encode(new Context(new \LogicException('something goes wrong')), 'line');
        
        $this->assertRegExp('/\[.*?\] LogicException: something goes wrong in file .*? on line .*?/', $result);
    }

    /**
     * @test
     */
    public function shouldUseCustomLineFormatToEncodeContext()
    {
        $customFormat = '%message%';
        
        $encoder = new LineEncoder($customFormat);

        $result = $encoder->encode(new Context(new \LogicException('something goes wrong')), 'line');

        $this->assertEquals('something goes wrong', $result);
    }

    /**
     * @test
     */
    public function shouldUseCustomDateFormatToEncodeContext()
    {
        $encoder = new LineEncoder(null, 'Y-m-d');

        $result = $encoder->encode(new Context(new \LogicException('something goes wrong')), 'line');

        $this->assertRegExp('/\[\d{4}-\d{2}-\d{2}\]/', $result);
    }

    /**
     * @test
     */
    public function shouldSetUnknownIfExceptionDoesNotHaveMessage()
    {
        $encoder = new LineEncoder(null, 'Y-m-d');

        $result = $encoder->encode(new Context(new \LogicException()), 'line');

        $this->assertContains('LogicException: unknown in file', $result);
    }
}