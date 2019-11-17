<?php

namespace Workflux\Tests\State;

use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Schema;
use Shrink0r\PhpSchema\SchemaInterface;
use Workflux\Error\InvalidInput;
use Workflux\Error\InvalidOutput;
use Workflux\Param\Input;
use Workflux\Param\Output;
use Workflux\State\StateInterface;
use Workflux\State\Validator;
use Workflux\Tests\TestCase;

final class ValidatorTest extends TestCase
{
    private static $default_schema = [ 'type' => 'assoc', 'properties' =>  [ ':any_name:' => [ 'type' => 'any' ] ] ];

    public function testGetInputSchema()
    {
        $validator = new Validator(
            new Schema('input_schema', self::$default_schema, new Factory),
            new Schema('output_schema', self::$default_schema, new Factory)
        );
        $this->assertInstanceOf(SchemaInterface::class, $validator->getInputSchema());
    }

    public function testGetOutputSchema()
    {
        $validator = new Validator(
            new Schema('input_schema', self::$default_schema, new Factory),
            new Schema('output_schema', self::$default_schema, new Factory)
        );
        $this->assertInstanceOf(SchemaInterface::class, $validator->getOutputSchema());
    }

    public function testValidateInput()
    {
        $validator = new Validator(
            new Schema('input_schema', self::$default_schema, new Factory),
            new Schema('output_schema', self::$default_schema, new Factory)
        );
        $mocked_state = $this->createMock(StateInterface::class);
        $validator->validateInput($mocked_state, new Input([ 'foo' => 'bar' ]));
    }

    public function testValidateOutput()
    {
        $validator = new Validator(
            new Schema('input_schema', self::$default_schema, new Factory),
            new Schema('output_schema', self::$default_schema, new Factory)
        );
        $mocked_state = $this->createMock(StateInterface::class);
        $validator->validateOutput($mocked_state, new Output('initial', [ 'foo' => 'bar' ]));
    }

    public function testInvalidInput()
    {
        $this->expectException(InvalidInput::class);
        $input_schema = self::$default_schema;
        $input_schema['properties'] = [ 'foo' => [ 'type' => 'bool', 'required' => true ] ];
        $validator = new Validator(
            new Schema('input_schema', $input_schema, new Factory),
            new Schema('output_schema', self::$default_schema, new Factory)
        );
        $mocked_state = $this->createMock(StateInterface::class);
        $validator->validateInput($mocked_state, new Input([ 'foo' => 'bar' ]));
    } // @codeCoverageIgnore

    public function testInvalidOutput()
    {
        $this->expectException(InvalidOutput::class);
        $output_schema = self::$default_schema;
        $output_schema['properties'] = [ 'foo' => [ 'type' => 'bool', 'required' => true ] ];
        $validator = new Validator(
            new Schema('input_schema', self::$default_schema, new Factory),
            new Schema('output_schema', $output_schema, new Factory)
        );
        $mocked_state = $this->createMock(StateInterface::class);
        $validator->validateOutput($mocked_state, new Output('initial', [ 'foo' => 'bar' ]));
    } // @codeCoverageIgnore
}
