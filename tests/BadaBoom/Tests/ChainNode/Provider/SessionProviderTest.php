<?php
namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\Provider\SessionProvider;
use BadaBoom\Context;

class SessionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Provider\SessionProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function shouldSetDefaultSectionNameIfNotProvided()
    {
        $context = new Context(new \Exception);

        $provider = new SessionProvider();
        $provider->handle($context);

        $this->assertTrue($context->hasVar('session'));
    }

    /**
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $context = new Context(new \Exception);

        $expectedCustomSectionName = 'custom_section_name';

        $provider = new SessionProvider($expectedCustomSectionName);
        $provider->handle($context);

        $this->assertTrue($context->hasVar($expectedCustomSectionName));
        $this->assertFalse($context->hasVar('session'));
    }

    /**
     * @test
     */
    public function shouldFillContextWithEmptyArrayIfGlobalSessionArrayNotDefinied()
    {
        unset($_SESSION);

        $context = new Context(new \Exception);

        $provider = new SessionProvider();
        $provider->handle($context);

        $this->assertEquals(array(), $context->getVar('session'));
    }

    /**
     * @test
     */
    public function shouldFillContextWithGlobalSessionArray()
    {
        $_SESSION = array();
        $_SESSION['foo'] = 'foo';
        $_SESSION['bar'] = 1;

        $context = new Context(new \Exception);

        $provider = new SessionProvider();
        $provider->handle($context);

        $this->assertEquals($_SESSION, $context->getVar('session'));
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

        $provider = new SessionProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($context);
    }

    protected function createChainNodeMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}