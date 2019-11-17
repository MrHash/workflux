<?php

namespace Workflux\Param;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\Param\ParamHolderTrait;

final class Output implements OutputInterface
{
    use ParamHolderTrait;

    /** @var string */
    private $current_state;

    public function __construct(string $current_state, array $params = [])
    {
        $this->current_state = $current_state;
        $this->params = $params;
    }

    public function getCurrentState(): string
    {
        return $this->current_state;
    }

    public function withCurrentState(string $current_state): OutputInterface
    {
        $output = clone $this;
        $output->current_state = $current_state;
        return $output;
    }

    public static function fromInput(string $current_state, InputInterface $input): OutputInterface
    {
        return new static($current_state, $input->toArray());
    }

    public function toArray(): array
    {
        return [ 'params' => $this->params, 'current_state' => $this->current_state ];
    }
}
