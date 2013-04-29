<?php
namespace BadaBoom\Tests\Bridge\Psr;

use BadaBoom\Bridge\Psr\Logger;
use BadaBoom\Context;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructed()
    {
        new Logger;
    }

    /**
     * @test
     */
    public function shouldImplementPSRLoggerInterface()
    {
        $this->assertInstanceOf('Psr\Log\LoggerInterface', new Logger);
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     */
    public function throwIfInvalidLevelGiven()
    {
        $logger = new Logger;
        $logger->log('invalid level', 'Foo');
    }

    /**
     * @test
     */
    public function shouldHandleAllChains()
    {
        $chain1 = $this->createMockChain();
        $chain1
            ->expects($this->once())
            ->method('handle')
        ;

        $chain2 = $this->createMockChain();
        $chain2
            ->expects($this->once())
            ->method('handle')
        ;

        $logger = new Logger;
        $logger->registerChain($chain1);
        $logger->registerChain($chain2);

        $logger->log('warning', 'message');
    }

    /**
     * @test
     *
     * @dataProvider provideLevelsAndMessages
     */
    public function shouldDoTheSameInLevelSpecificMethodAndGeneralAtAllLevels($level)
    {
        $chain = $this->createMockChain();
        $chain
            ->expects($this->at(0))
            ->method('handle')
            ->will($this->returnCallback(function($context) use (&$actualFirstContext) {
                $actualFirstContext = $context;
            }))
        ;
        $chain
            ->expects($this->at(1))
            ->method('handle')
            ->will($this->returnCallback(function($context) use (&$actualSecondContext) {
                $actualSecondContext = $context;
            }))
        ;

        $logger = new Logger;
        $logger->registerChain($chain);

        $exception = new \Exception($level);

        $logger->{$level}($exception);
        $logger->log($level, $exception);

        $this->assertEquals($actualFirstContext, $actualSecondContext);
    }

    public function provideLevelsAndMessages()
    {
        return array(
            array(LogLevel::EMERGENCY),
            array(LogLevel::ALERT),
            array(LogLevel::CRITICAL),
            array(LogLevel::ERROR),
            array(LogLevel::WARNING),
            array(LogLevel::NOTICE),
            array(LogLevel::INFO),
            array(LogLevel::DEBUG),
        );
    }

    /**
     * @test
     */
    public function shouldFallowCorrectOrderOfHandlingParameters()
    {
        /** @var Context $actualContext */
        $actualContext = null;

        $chain = $this->createMockChain();
        $chain
            ->expects($this->any())
            ->method('handle')
            ->will($this->returnCallback(function($context) use (&$actualContext) {
                $actualContext = $context;
            }))
        ;

        $logger = new Logger;
        $logger->registerChain($chain);

        $exceptionAsMessage = new \Exception('Exception 1');
        $exceptionInContext = new \Exception('Exception 2');
        $messageString = 'sting message';
        $messageObject = new ClassWithToString();
        $messageObjectWithoutToString = new \stdClass();

        $logger->log('warning', $exceptionAsMessage, array('exception' => $exceptionInContext));
        $this->assertEquals($exceptionAsMessage, $actualContext->getException());

        $logger->log('warning', $messageString, array('exception' => $exceptionInContext));
        $this->assertEquals($exceptionInContext, $actualContext->getException());

        $logger->log('warning', $messageString);
        $this->assertEquals($messageString, $actualContext->getException()->getMessage());

        $logger->log('warning', $messageObject);
        $this->assertEquals('Object with to string', $actualContext->getException()->getMessage());

        $logger->log('warning', $messageObjectWithoutToString);
        $this->assertEquals(
            'Message of unexpected type given! stdClass does not have __toString() method.',
            $actualContext->getException()->getMessage()
        );

        $logger->log('warning', array());
        $this->assertEquals(
            'Message of unexpected type given! array instead of string.',
            $actualContext->getException()->getMessage()
        );
    }

    /**
     * @test
     */
    public function shouldReplaceMessageVariablesButNotInException()
    {
        /** @var Context $actualContext */
        $actualContext = null;

        $chain = $this->createMockChain();
        $chain
            ->expects($this->any())
            ->method('handle')
            ->will($this->returnCallback(function($context) use (&$actualContext) {
                $actualContext = $context;
            }))
        ;

        $logger = new Logger;
        $logger->registerChain($chain);

        $logger->log('warning', 'Hello {user} {name} {age} {$illegalVar} {legal_VAR.1} {exists}', array(
            'user' => 'TestUser',
            'name' => new ClassWithToString(),
            'age' => 99,
            'legal_VAR.1' => 'LEGAL',
            'illegal var' => 'NOT LEGAL',
            'notExistent' => 'NOT Exist',
        ));
        $this->assertEquals(
            'Hello TestUser {name} 99 {$illegalVar} LEGAL {exists}',
            $actualContext->getException()->getMessage()
        );

        $data = array('name' => 'Test');
        $exceptionMassageWithVariable = 'exception message {name}';

        $logger->log('warning', new \Exception($exceptionMassageWithVariable), $data);
        $this->assertEquals($exceptionMassageWithVariable, $actualContext->getException()->getMessage());
    }

    /**
     * @test
     */
    public function shouldFillContextData()
    {
        /** @var Context $actualContext */
        $actualContext = null;

        $chain = $this->createMockChain();
        $chain
            ->expects($this->any())
            ->method('handle')
            ->will($this->returnCallback(function($context) use (&$actualContext) {
                $actualContext = $context;
            }))
        ;

        $logger = new Logger;
        $logger->registerChain($chain);

        $data = array('name' => 'Test');

        $logger->log('warning', new \Exception('exception message'), $data);
        $this->assertEquals(
            array(
                'level' => 'warning',
                'message' => 'exception message',
                'data' => $data,
            ),
            $actualContext->getVar('log')
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockChain()
    {
        return $this->getMockForAbstractClass('BadaBoom\ChainNode\AbstractChainNode');
    }
}

class ClassWithToString
{
    public function __toString() {
        return 'Object with to string';
    }
}