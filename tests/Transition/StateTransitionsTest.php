<?php

namespace Workflux\Tests\Transition;

use Workflux\Error\InvalidStructure;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\StateMap;
use Workflux\Tests\TestCase;
use Workflux\Transition\StateTransitions;
use Workflux\Transition\Transition;
use Workflux\Transition\TransitionSet;

final class StateTransitionsTest extends TestCase
{
    public function testCount()
    {
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final')
        ]);
        $state_transitions = new StateTransitions($state_map, $transition_set);
        $this->assertCount(count($transition_set), $state_transitions);
    }

    public function testHas()
    {
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final')
        ]);
        $state_transitions = new StateTransitions($state_map, $transition_set);
        $this->assertTrue($state_transitions->has('initial'));
        $this->assertFalse($state_transitions->has('baz'));
    }

    public function testToArray()
    {
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final')
        ]);
        $state_transitions = new StateTransitions($state_map, $transition_set);
        $expected_transition_sets = [ 'initial', 'foo', 'bar', 'foobar' ];
        $state_transitions_array = $state_transitions->toArray();
        foreach ($expected_transition_sets as $expected_transition_set) {
            $this->assertInstanceOf(TransitionSet::class, $state_transitions_array[$expected_transition_set]);
        }
    }

    public function testNonExistantToState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to transition to unknown state: foobaz'
        );
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobaz'),
            new Transition('foobar', 'final')
        ]);
        new StateTransitions($state_map, $transition_set);
    } // @codeCoverageIgnore

    public function testNonExistantFromState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to transition from unknown state: fu'
        );
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('fu', 'bar'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final')
        ]);
        new StateTransitions($state_map, $transition_set);
    } // @codeCoverageIgnore

    public function testTransitionToInitialState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to transition to initial-state: initial'
        );
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'initial'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final')
        ]);
        new StateTransitions($state_map, $transition_set);
    } // @codeCoverageIgnore

    public function testTransitionFromFinalState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to transition from final-state: final'
        );
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobar'),
            new Transition('foobar', 'final'),
            new Transition('final', 'foo')
        ]);
        new StateTransitions($state_map, $transition_set);
    } // @codeCoverageIgnore

    public function testStatesNotConnected()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Not all states are properly connected.'
        );
        $state_map = new StateMap($this->buildStateArray());
        $transition_set = new TransitionSet([
            new Transition('initial', 'foo'),
            new Transition('foo', 'bar'),
            new Transition('bar', 'foobar')
        ]);
        new StateTransitions($state_map, $transition_set);
    } // @codeCoverageIgnore

    private function buildStateArray()
    {
        return [
            $this->createState('initial', InitialState::class),
            $this->createState('foo'),
            $this->createState('bar'),
            $this->createState('foobar'),
            $this->createState('final', FinalState::class)
        ];
    }
}
