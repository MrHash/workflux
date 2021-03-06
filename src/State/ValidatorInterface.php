<?php

namespace Workflux\State;

use Shrink0r\PhpSchema\SchemaInterface;
use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\State\StateInterface;

interface ValidatorInterface
{
    public function validateInput(StateInterface $state, InputInterface $input): void;

    public function validateOutput(StateInterface $state, OutputInterface $output): void;

    public function getInputSchema(): SchemaInterface;

    public function getOutputSchema(): SchemaInterface;
}
