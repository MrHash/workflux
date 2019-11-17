<?php

namespace Workflux\State;

use Workflux\State\StateInterface;
use Workflux\State\StateTrait;

final class InteractiveState implements StateInterface
{
    use StateTrait;

    public function isInteractive(): bool
    {
        return true;
    }
}
