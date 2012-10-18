<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\ServerProvider;
use BadaBoom\Context;

class ServerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\ServerProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception);

        $provider = new ServerProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('server'));
    }

    /**
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception);

        $expectedCustomSectionName = 'custom_section_name';

        $provider = new ServerProvider($expectedCustomSectionName);
        $provider->handle($context);

        $this->assertTrue($context->hasVar($expectedCustomSectionName));
        $this->assertFalse($context->hasVar('server'));
    }

    /**
     * @test
     */
    public function shouldFillContextWithGlobalEnvArray()
    {
        $_SERVER = array(
            'foo' => 'bar'
        );

        $context = new Context(new \Exception);

        $provider = new ServerProvider();
        $provider->handle($context);

        $this->assertEquals($_SERVER, $context->getVar('server'));
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

        $provider = new ServerProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($context);
    }

    protected function createChainNodeMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}