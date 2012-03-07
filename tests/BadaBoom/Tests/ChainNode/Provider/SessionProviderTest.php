<?php

namespace BadaBoom\Tests\ChainNode\Provider;

use BadaBoom\DataHolder\DataHolder;
use BadaBoom\ChainNode\Provider\SessionProvider;

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
        $data = new DataHolder;

        $provider = new SessionProvider();
        $provider->handle(new \Exception(), $data);

        $this->assertTrue($data->has('session'));
    }

    /**
     * @test
     */
    public function shouldUseCustomSectionNameIfProvided()
    {
        $data = new DataHolder;

        $expectedCustomSectionName = 'custom_section_name';

        $provider = new SessionProvider($expectedCustomSectionName);
        $provider->handle(new \Exception(), $data);

        $this->assertTrue($data->has($expectedCustomSectionName));
    }

    /**
     * @test
     */
    public function shouldFillDataHolderWithEmptyArrayIfGlobalSessionArrayNotDefinied()
    {
        unset($_SESSION);

        $data = new DataHolder;

        $provider = new SessionProvider();
        $provider->handle(new \Exception(), $data);

        $this->assertEquals(array(), $data->get('session'));
    }

    /**
     * @test
     */
    public function shouldFillDataHolderWithGlobalSessionArray()
    {
        $_SESSION = array();
        $_SESSION['foo'] = 'foo';
        $_SESSION['bar'] = 1;

        $data = new DataHolder;

        $provider = new SessionProvider();
        $provider->handle(new \Exception(), $data);

        $this->assertEquals($_SESSION, $data->get('session'));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $expectedExcpetion = new \Exception;
        $expectedDataHolder = new DataHolder;

        $nextChainNode = $this->createChainNodeMock();
        $nextChainNode
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedExcpetion),
                $this->equalTo($expectedDataHolder)
            )
        ;

        $provider = new SessionProvider();
        $provider->nextNode($nextChainNode);

        $provider->handle($expectedExcpetion, $expectedDataHolder);
    }

    protected function createChainNodeMock()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}