<?php

namespace Workflux\Tests\State;

use Workflux\Error\ConfigError;
use Workflux\Param\Input;
use Workflux\Param\OutputInterface;
use Workflux\Param\Settings;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\InteractiveState;
use Workflux\State\State;
use Workflux\State\ValidatorInterface;
use Workflux\Tests\State\Fixture\StateWithRequiredSettings;
use Workflux\Tests\TestCase;

final class StateTest extends TestCase
{
    public function testExecute()
    {
        $state = $this->createState('foobar');
        $output = $state->execute(new Input([ 'foo' => 'bar' ]));
        $this->assertInstanceOf(OutputInterface::class, $output);
    }

    public function testGetName()
    {
        $state = $this->createState('foobar');
        $this->assertEquals('foobar', $state->getName());
    }

    public function testIsFinal()
    {
        $this->assertFalse($this->createState('foobar')->isFinal());
        $this->assertTrue($this->createState('foobar', FinalState::class)->isFinal());
    }

    public function testIsInitial()
    {
        $this->assertFalse($this->createState('foobar')->isInitial());
        $this->assertTrue($this->createState('foobar', InitialState::class)->isInitial());
    }

    public function testIsInteractive()
    {
        $this->assertFalse($this->createState('foobar')->isInteractive());
        $this->assertTrue($this->createState('foobar', InteractiveState::class)->isInteractive());
    }

    public function testGetValidator()
    {
        $state = $this->createState('foobar');
        $this->assertInstanceOf(ValidatorInterface::class, $state->getValidator());
    }

    public function testGetSettings()
    {
        $state = $this->createState('foobar', State::class, new Settings([ 'foo' => 'bar' ]));
        $this->assertInstanceOf(Settings::class, $state->getSettings());
        $this->assertEquals('bar', $state->getSettings()->get('foo'));
    }

    public function testGetSetting()
    {
        $state = $this->createState('foobar', State::class, new Settings([ 'foo' => 'bar' ]));
        $this->assertEquals('bar', $state->getSetting('foo'));
    }

    public function testMissingRequiredSetting()
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage(
            "Trying to configure state 'foobar' without required setting 'foobar'."
        );
        $this->createState('foobar', StateWithRequiredSettings::class);
    } // @codeCoverageIgnore
}
