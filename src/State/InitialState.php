<?php

namespace Workflux\State;

use Workflux\State\StateInterface;
use Workflux\State\StateTrait;

final class InitialState implements StateInterface
{
    use StateTrait;

    public function isInitial(): bool
    {
        return true;
    }
}
