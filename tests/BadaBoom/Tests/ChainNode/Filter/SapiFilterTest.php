<?php
namespace BadaBoom\Tests\ChainNode\Filter;

use BadaBoom\ChainNode\Filter\SapiFilter;
use BadaBoom\Context;

class SapiFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Fumocker\Fumocker
     */
    protected $fumocker;

    public function setUp()
    {
        if (false == class_exists('Fumocker\Fumocker')) {
            $this->markTestSkipped('These test requires Fumocker lib.');
        }
        
        $this->fumocker = new \Fumocker\Fumocker();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $this->fumocker->cleanup();
    }

    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractFilter()
    {
        $rc = new \ReflectionClass('BadaBoom\ChainNode\Filter\SapiFilter');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Filter\AbstractFilter'));
    }

    /**
     * @test
     */
    public function shouldAllowAllByDefault()
    {
        $filter = new SapiFilter();

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowToSetDenyAll()
    {
        $filter = new SapiFilter();

        $filter->denyAll();
    }

    /**
     * @test
     */
    public function shouldNotPropagateIfDenyAll()
    {
        $filter = new SapiFilter();

        $filter->denyAll();

        $this->assertFalse($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowToSetAllowAll()
    {
        $filter = new SapiFilter();

        $filter->allowAll();
    }

    /**
     * @test
     */
    public function shouldPropagateIfAllowAll()
    {
        $filter = new SapiFilter();

        $filter->allowAll();

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldOverwriteDefaultRuleIfCalledSeveralTimes()
    {
        $filter = new SapiFilter();

        $filter->allowAll();
        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));

        $filter->denyAll();
        $this->assertFalse($filter->shouldContinue(new Context(new \Exception)));

        $filter->allowAll();
        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowToSetDeniedSapiName()
    {
        $filter = new SapiFilter();

        $filter->deny('a_sapi_name');
    }

    /**
     * @test
     */
    public function shouldAllowToSetAllowedSapiName()
    {
        $filter = new SapiFilter();

        $filter->allow('a_sapi_name');
    }

    /**
     * @test
     */
    public function shouldDeniedCurrentSapiModeIfMatchDenied()
    {
        $deniedSapiName = 'denied_sapi_name';

        $phpSapiNameFunctionMock = $this->fumocker->getMock('BadaBoom\ChainNode\Filter', 'php_sapi_name');
        $phpSapiNameFunctionMock
            ->expects($this->once())
            ->method('php_sapi_name')
            ->will($this->returnValue($deniedSapiName))
        ;

        $filter = new SapiFilter();

        //guard
        $filter->allowAll();

        $filter->deny($deniedSapiName);

        $this->assertFalse($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldAllowCurrentSapiModeIfMatchAllowed()
    {
        $allowedSapiName = 'allowed_sapi_name';

        $phpSapiNameFunctionMock = $this->fumocker->getMock('BadaBoom\ChainNode\Filter', 'php_sapi_name');
        $phpSapiNameFunctionMock
            ->expects($this->once())
            ->method('php_sapi_name')
            ->will($this->returnValue($allowedSapiName))
        ;

        $filter = new SapiFilter();

        //guard
        $filter->denyAll();

        $filter->allow($allowedSapiName);

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldOverwriteDeniedRuleByAllowed()
    {
        $sapiName = 'sapi_name';

        $phpSapiNameFunctionMock = $this->fumocker->getMock('BadaBoom\ChainNode\Filter', 'php_sapi_name');
        $phpSapiNameFunctionMock
            ->expects($this->once())
            ->method('php_sapi_name')
            ->will($this->returnValue($sapiName))
        ;

        $filter = new SapiFilter();

        //guard
        $filter->denyAll();

        $filter->deny($sapiName);
        $filter->allow($sapiName);

        $this->assertTrue($filter->shouldContinue(new Context(new \Exception)));
    }

    /**
     * @test
     */
    public function shouldOverwriteAllowedRuleByDenied()
    {
        $sapiName = 'sapi_name';

        $phpSapiNameFunctionMock = $this->fumocker->getMock('BadaBoom\ChainNode\Filter', 'php_sapi_name');
        $phpSapiNameFunctionMock
            ->expects($this->once())
            ->method('php_sapi_name')
            ->will($this->returnValue($sapiName))
        ;

        $filter = new SapiFilter();

        //guard
        $filter->allowAll();

        $filter->allow($sapiName);
        $filter->deny($sapiName);

        $this->assertFalse($filter->shouldContinue(new Context(new \Exception)));
    }
}