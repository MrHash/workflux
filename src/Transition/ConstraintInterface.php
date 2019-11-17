<?php

namespace Workflux\Transition;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;

interface ConstraintInterface
{
    public function accepts(InputInterface $input, OutputInterface $output): bool;

    public function __toString(): string;
}
