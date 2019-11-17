<?php

namespace Workflux;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\State\StateInterface;
use Workflux\State\StateMap;
use Workflux\Transition\StateTransitions;

interface StateMachineInterface
{
    public function getName(): string;

    public function getInitialState(): StateInterface;

    public function getFinalStates(): StateMap;

    public function getStates(): StateMap;

    public function getStateTransitions(): StateTransitions;

    public function execute(InputInterface $input, string $start_state = null): OutputInterface;
}
