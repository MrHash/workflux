<?php

namespace Workflux\Tests\State;

use Workflux\Error\InvalidStructure;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\StateInterface;
use Workflux\State\StateMap;
use Workflux\State\StateSet;
use Workflux\Tests\State\Fixture\TwoFaceState;
use Workflux\Tests\TestCase;

final class StateSetTest extends TestCase
{
    public function testCount()
    {
        $state_set = new StateSet($this->buildStateArray());
        $this->assertCount(5, $state_set);
    }

    public function testSplat()
    {
        $state_set = new StateSet($this->buildStateArray());
        list($initial_state, $all_states, $final_states) = $state_set->splat();
        $this->assertInstanceOf(StateInterface::class, $initial_state);
        $this->assertTrue($initial_state->isInitial());
        $this->assertCount(5, $all_states);
        $this->assertInstanceOf(StateMap::class, $all_states);
        $this->assertCount(1, $final_states);
        $this->assertInstanceOf(StateMap::class, $final_states);
        $this->assertTrue($final_states->get('final')->isFinal());
    }

    public function testGetIterator()
    {
        $states_array = $this->buildStateArray();
        $state_set = new StateSet($states_array);
        $i = 0;
        foreach ($state_set as $state) {
            $this->assertEquals($states_array[$i], $state);
            $i++;
        }
        $this->assertEquals(count($states_array), $i);
    }

    public function testToArray()
    {
        $states_array = $this->buildStateArray();
        $state_set = new StateSet($states_array);
        $this->assertEquals($states_array, $state_set->toArray());
    }

    public function testMultipleInitialStates()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to add more than one initial state.'
        );
        $states_array = $this->buildStateArray();
        $states_array[] = $this->createState('snafu', InitialState::class);
        $state_set = new StateSet($states_array);
        $state_set->splat();
    } // @codeCoverageIgnore

    public function testInconsistentType()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to add state as initial and final at the same time.'
        );
        $states_array = $this->buildStateArray();
        $states_array[] = $this->createState('snafu', TwoFaceState::class);
        $state_set = new StateSet($states_array);
        $state_set->splat();
    } // @codeCoverageIgnore

    public function testMissingInitialState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to create statemachine without an initial state.'
        );
        $states_array = $this->buildStateArray();
        array_shift($states_array);
        $state_set = new StateSet($states_array);
        $state_set->splat();
    } // @codeCoverageIgnore

    public function testMissingFinalState()
    {
        $this->expectException(InvalidStructure::class);
        $this->expectExceptionMessage(
            'Trying to create statemachine without at least one final state.'
        );
        $states_array = $this->buildStateArray();
        array_pop($states_array);
        $state_set = new StateSet($states_array);
        $state_set->splat();
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
