<?php

namespace Workflux\Param;

use Workflux\Param\OutputInterface;

interface InputInterface extends ParamHolderInterface
{
    public static function fromOutput(OutputInterface $input): self;

    public function getEvent(): string;

    public function hasEvent(): bool;

    public function withEvent(string $event): self;
}
