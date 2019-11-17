<?php

namespace Workflux\Tests\Builder;

use Symfony\Component\Yaml\Parser;
use Workflux\Builder\ArrayStateMachineBuilder;
use Workflux\Error\ConfigError;
use Workflux\Error\MissingImplementation;
use Workflux\Param\Input;
use Workflux\Tests\TestCase;

final class ArrayStateMachineBuilderTest extends TestCase
{
    public function testBuild()
    {
        $state_machine = (new ArrayStateMachineBuilder($this->fixture('statemachine')))->build();

        $initial_input = new Input([ 'transcoding_required' => true ]);
        $initial_output = $state_machine->execute($initial_input);
        $current_state = $initial_output->getCurrentState();
        $this->assertEquals('transcoding', $current_state);
        $input = Input::fromOutput($initial_output)->withEvent('video_transcoded');
        $final_output = $state_machine->execute($input, $current_state);
        $this->assertEquals('ready', $final_output->getCurrentState());
    }

    public function testNonStringConstraint()
    {
        (new ArrayStateMachineBuilder($this->fixture('non_string_constraint')))->build();
    }

    public function testEmptyConfig()
    {
        $this->expectException(ConfigError::class);
        (new ArrayStateMachineBuilder([]))->build();
    } // @codeCoverageIgnore

    public function testInvalidStateMachineSchema()
    {
        $this->expectException(ConfigError::class);
        (new ArrayStateMachineBuilder($this->fixture('invalid_schema')))->build();
    } // @codeCoverageIgnore

    public function testInconsistentInitialState()
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage(
            "Trying to provide custom state that isn't initial but marked as initial in config."
        );
        (new ArrayStateMachineBuilder($this->fixture('inconsistent_initial')))->build();
    } // @codeCoverageIgnore

    public function testInconsistentInteractiveState()
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage(
            "Trying to provide custom state that isn't interactive but marked as interactive in config."
        );
        (new ArrayStateMachineBuilder($this->fixture('inconsistent_interactive')))->build();
    } // @codeCoverageIgnore

    public function testInconsistentFinalState()
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage(
            "Trying to provide custom state that isn't final but marked as final in config."
        );
        (new ArrayStateMachineBuilder($this->fixture('inconsistent_final')))->build();
    } // @codeCoverageIgnore

    public function testNonImplementedState()
    {
        $this->expectException(MissingImplementation::class);
        (new ArrayStateMachineBuilder($this->fixture('non_implemented_state')))->build();
    } // @codeCoverageIgnore

    public function testNonImplementedTransition()
    {
        $this->expectException(MissingImplementation::class);
        (new ArrayStateMachineBuilder($this->fixture('non_implemented_transition')))->build();
    } // @codeCoverageIgnore

    /**
     * @param string $name
     *
     * @return string
     */
    private function fixture(string $name): array
    {
        return (new Parser)->parse(file_get_contents(__DIR__.'/Fixture/Yaml/'.$name.'.yaml'));
    }
}
