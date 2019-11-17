<?php

namespace Workflux\Builder;

use Workflux\StateMachineInterface;

interface StateMachineBuilderInterface
{
    public function build(): StateMachineInterface;
}
