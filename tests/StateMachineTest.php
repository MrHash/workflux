<?php

namespace Workflux\Tests;

use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Schema;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Workflux\Error\CorruptExecutionFlow;
use Workflux\Error\ExecutionError;
use Workflux\Param\Input;
use Workflux\Param\Settings;
use Workflux\StateMachine;
use Workflux\State\FinalState;
use Workflux\State\InitialState;
use Workflux\State\InteractiveState;
use Workflux\State\StateSet;
use Workflux\Tests\Fixture\InactiveTransition;
use Workflux\Tests\TestCase;
use Workflux\Transition\ExpressionConstraint;
use Workflux\Transition\Transition;
use Workflux\Transition\TransitionSet;

final class StateMachineTest extends TestCase
{
    public function testExecute()
    {
        $schema = new Schema(
            'default_schema',
            [ 'type' => 'assoc', 'properties' => [ 'is_ready' => [ 'type' => 'bool' ] ] ],
            new Factory
        );
        $states = new StateSet([
            $this->createState('initial', InitialState::class, null, $schema),
            $this->createState('foobar'),
            $this->createState('bar', InteractiveState::class),
            $this->createState('final', FinalState::class)
        ]);
        $transitions = (new TransitionSet)
            ->add(new Transition(
                'initial',
                'foobar',
                new Settings,
                [ new ExpressionConstraint('input.get("is_ready") == true', new ExpressionLanguage) ]
            ))
            ->add(new Transition('foobar', 'bar'))
            ->add(new Transition('bar', 'final'));
        $statemachine = new StateMachine('test-machine', $states, $transitions);
        $intial_output = $statemachine->execute(new Input([ 'is_ready' => true ]), 'initial');
        $input = Input::fromOutput($intial_output)->withEvent('on_signal');
        $output = $statemachine->execute($input, $intial_output->getCurrentState());
        $this->assertEquals('final', $output->getCurrentState());
    }

    public function testGetName()
    {
        $statemachine = $this->buildStateMachine();
        $this->assertEquals('test-machine', $statemachine->getName());
    }

    public function testGetInitialState()
    {
        $statemachine = $this->buildStateMachine();
        $this->assertEquals('initial', $statemachine->getInitialState()->getName());
    }

    public function testGetStates()
    {
        $statemachine = $this->buildStateMachine();
        $this->assertCount(6, $statemachine->getStates());
    }

    public function testFinalStates()
    {
        $statemachine = $this->buildStateMachine();
        $this->assertCount(1, $statemachine->getFinalStates());
    }

    public function testGetStateTransitions()
    {
        $statemachine = $this->buildStateMachine();
        $this->assertCount(5, $statemachine->getStateTransitions());
    }

    public function testMultipleActivatedTransitions()
    {
        $this->expectException(ExecutionError::class);
        $this->expectExceptionMessage(
            'Trying to activate more than one transition at a time. '.
            'Transition: approval -> published was activated first. Now approval -> archive is being activated too.'
        );

        $states = new StateSet([
            $this->createState('initial', InitialState::class),
            $this->createState('edit'),
            $this->createState('approval'),
            $this->createState('published'),
            $this->createState('archive'),
            $this->createState('final', FinalState::class)
        ]);
        $transitions = (new TransitionSet)
            ->add(new Transition('initial', 'edit'))
            ->add(new Transition('edit', 'approval'))
            ->add(new Transition('approval', 'published'))
            ->add(new Transition('approval', 'archive'))
            ->add(new Transition('published', 'archive'))
            ->add(new Transition('archive', 'final'));
        $statemachine = new StateMachine('test-machine', $states, $transitions);
        $statemachine->execute(new Input);
    } // @codeCoverageIgnore

    public function testInfiniteExecutionLoop()
    {
        $this->expectException(CorruptExecutionFlow::class);
        $this->expectExceptionMessage(
            'Trying to execute more than the allowed number of 20 workflow steps.
Looks like there is a loop between: approval -> published -> archive'
        );

        $states = new StateSet([
            $this->createState('initial', InitialState::class),
            $this->createState('edit'),
            $this->createState('approval'),
            $this->createState('published'),
            $this->createState('archive'),
            $this->createState('final', FinalState::class)
        ]);
        $transitions = (new TransitionSet)
            ->add(new Transition('initial', 'edit'))
            ->add(new Transition('edit', 'approval'))
            ->add(new Transition('approval', 'published'))
            ->add(new Transition('published', 'archive'))
            ->add(new Transition('archive', 'approval'))
            ->add(new InactiveTransition('archive', 'final'));
        $statemachine = new StateMachine('test-machine', $states, $transitions);
        $statemachine->execute(new Input);
    } // @codeCoverageIgnore

    public function testResumeOnFinalState()
    {
        $this->expectException(ExecutionError::class);
        $this->expectExceptionMessage(
            'Trying to (re)execute statemachine at final state: final'
        );
        $statemachine = $this->buildStateMachine();
        $statemachine->execute(new Input, 'final');
    } // @codeCoverageIgnore

    public function testResumeOnUnknownState()
    {
        $this->expectException(ExecutionError::class);
        $this->expectExceptionMessage(
            'Trying to start statemachine execution at unknown state: baz'
        );
        $statemachine = $this->buildStateMachine();
        $statemachine->execute(new Input, 'baz');
    } // @codeCoverageIgnore

    public function testResumeWithoutEvent()
    {
        $this->expectException(ExecutionError::class);
        $this->expectExceptionMessage(
            'Trying to resume statemachine executing without providing an event/signal.'
        );
        $statemachine = $this->buildStateMachine();
        $output = $statemachine->execute(new Input);
        $statemachine->execute(Input::fromOutput($output), $output->getCurrentState());
    } // @codeCoverageIgnore

    private function buildStateMachine()
    {
        $states = new StateSet([
            $this->createState('initial', InitialState::class),
            $this->createState('edit'),
            $this->createState('approval', InteractiveState::class),
            $this->createState('published'),
            $this->createState('archive'),
            $this->createState('final', FinalState::class)
        ]);
        $transitions = (new TransitionSet)
            ->add(new Transition('initial', 'edit'))
            ->add(new Transition('edit', 'approval'))
            ->add(new Transition('approval', 'published'))
            ->add(new Transition('published', 'archive'))
            ->add(new Transition('archive', 'final'));
        return new StateMachine('test-machine', $states, $transitions);
    }
}
