<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\EnvironmentProvider;
use BadaBoom\Context;

class EnvironmentProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\EnvironmentProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception);

        $provider = new EnvironmentProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('env'));
    }

    /**
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception);

        $expectedCustomSectionName = 'custom_section_name';

        $provider = new EnvironmentProvider($expectedCustomSectionName);
        $provider->handle($context);

        $this->assertTrue($context->hasVar($expectedCustomSectionName));
        $this->assertFalse($context->hasVar('env'));
    }

    /**
     * @test
     */
    public function shouldFillContextWithGlobalEnvArray()
    {
        $_ENV = array(
            'foo' => 'bar'
        );

        $context = new Context(new \Exception);

        $provider = new EnvironmentProvider();
        $provider->handle($context);

        $this->assertEquals($_ENV, $context->getVar('env'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception);

        $nextChainNode = $this->createChainNodeMock();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $provider = new EnvironmentProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($context);
    }

    protected function createChainNodeMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}