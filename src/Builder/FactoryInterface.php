<?php

namespace Workflux\Builder;

use Workflux\State\StateInterface;
use Workflux\Transition\TransitionInterface;

interface FactoryInterface
{
    public function createState(string $name, array $state = null): StateInterface;

    public function createTransition(string $from, string $to, array $config = null): TransitionInterface;
}
