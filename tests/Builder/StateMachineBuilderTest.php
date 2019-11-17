<?php

namespace Workflux\Tests\Builder;

use Workflux\Builder\StateMachineBuilder;
use Workflux\Error\InvalidStructure;
use Workflux\Error\MissingImplementation;
use Workflux\Error\UnknownState;
use Workflux\StateMachine;
use Workflux\StateMachineInterface;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\InteractiveState;
use Workflux\Tests\Builder\Fixture\EmptyClass;
use Workflux\Tests\TestCase;
use Workflux\Transition\Transition;

final class StateMachineBuilderTest extends TestCase
{
    public function testBuild()
    {
        $state_machine = (new StateMachineBuilder(StateMachine::class))
            ->addStateMachineName('video-transcoding')
            ->addState($this->createState('initial', InitialState::class))
            ->addStates([
                $this->createState('state1', InteractiveState::class),
                $this->createState('state2'),
                $this->createState('final', FinalState::class)
            ])
            ->addTransition(new Transition('initial', 'state1'))
            ->addTransitions([
                new Transition('state1', 'state2'),
                new Transition('state2', 'final')
            ])
            ->build();
        $this->assertInstanceOf(StateMachineInterface::class, $state_machine);
        $this->assertEquals('video-transcoding', $state_machine->getName());
    }

    public function testMissingInterface()
    {
        $this->expectException(MissingImplementation::class);
        new StateMachineBuilder(EmptyClass::class);
    } // @codeCoverageIgnore

    public function testNonExistantClass()
    {
        $this->expectException(MissingImplementation::class);
        new StateMachineBuilder('FooBarMachine');
    } // @codeCoverageIgnore

    public function testUnknownFromState()
    {
        $this->expectException(UnknownState::class);
        (new StateMachineBuilder(StateMachine::class))
            ->addStateMachineName('video-transcoding')
            ->addState($this->createState('initial', InitialState::class))
            ->addState($this->createState('state1'))
            ->addState($this->createState('final', FinalState::class))
            ->addTransition(new Transition('start', 'state1'));
    } // @codeCoverageIgnore

    public function testUnknownToState()
    {
        $this->expectException(UnknownState::class);
        (new StateMachineBuilder(StateMachine::class))
            ->addStateMachineName('video-transcoding')
            ->addState($this->createState('initial', InitialState::class))
            ->addState($this->createState('state1'))
            ->addState($this->createState('final', FinalState::class))
            ->addTransition(new Transition('state1', 'state2'));
    } // @codeCoverageIgnore

    public function testDuplicateTransition()
    {
        $this->expectException(InvalidStructure::class);
        (new StateMachineBuilder(StateMachine::class))
            ->addStateMachineName('video-transcoding')
            ->addState($this->createState('initial', InitialState::class))
            ->addState($this->createState('state1'))
            ->addState($this->createState('final', FinalState::class))
            ->addTransition(new Transition('initial', 'state1'))
            ->addTransition(new Transition('initial', 'state1'));
    } // @codeCoverageIgnore
}
