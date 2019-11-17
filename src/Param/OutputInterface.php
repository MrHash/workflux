<?php

namespace Workflux\Param;

use Workflux\Param\InputInterface;
use Workflux\Param\ParamHolderInterface;

interface OutputInterface extends ParamHolderInterface
{
    public static function fromInput(string $current_state, InputInterface $input): self;

    public function getCurrentState(): string;
}
