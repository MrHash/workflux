<?php

namespace Workflux\Tests\Builder;

use Workflux\Builder\StateMachineBuilder;
use Workflux\Param\Settings;
use Workflux\StateMachine;
use Workflux\StateMachineInterface;
use Workflux\State\Breakpoint;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\State;
use Workflux\Tests\TestCase;
use Workflux\Transition\Transition;

class StateMachineBuilderTest extends TestCase
{
    public function testBuild()
    {
        $state_machine = (new StateMachineBuilder)
            ->addStateMachineName('video-transcoding')
            ->addState(new InitialState('initial', new Settings))
            ->addStates([
                new Breakpoint('foobar', new Settings),
                new State('bar', new Settings),
                new FinalState('final', new Settings)
            ])
            ->addTransition(new Transition('initial', 'foobar', new Settings))
            ->addTransitions([
                new Transition('foobar', 'bar', new Settings),
                new Transition('bar', 'final', new Settings)
            ])
            ->build(StateMachine::CLASS);

        $this->assertInstanceOf(StateMachineInterface::CLASS, $state_machine);
    }
}
