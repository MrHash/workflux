<?php

namespace Workflux\Transition;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;

interface TransitionInterface
{
    public function isActivatedBy(InputInterface $input, OutputInterface $output): bool;

    public function getFrom(): string;

    public function getTo(): string;

    public function getLabel(): string;

    public function getConstraints(): array;

    public function hasConstraints(): bool;
}
