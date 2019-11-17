<?php

namespace Workflux\Renderer;

use Workflux\StateMachineInterface;

interface RendererInterface
{
    /** @return mixed */
    public function render(StateMachineInterface $state_machine);
}
