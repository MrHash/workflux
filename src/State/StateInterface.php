<?php

namespace Workflux\State;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\Param\ParamHolderInterface;
use Workflux\State\ValidatorInterface;

interface StateInterface
{
    public function execute(InputInterface $input): OutputInterface;

    public function getName(): string;

    public function isInitial(): bool;

    public function isFinal(): bool;

    public function isInteractive(): bool;

    public function getValidator(): ValidatorInterface;

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $name, $default = null);

    public function getSettings(): ParamHolderInterface;
}
