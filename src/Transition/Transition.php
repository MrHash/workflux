<?php

namespace Workflux\Transition;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\Param\ParamHolderInterface;
use Workflux\Param\Settings;
use Workflux\Transition\ConstraintInterface;
use Workflux\Transition\TransitionInterface;

final class Transition implements TransitionInterface
{
    /** @var string */
    private $from;

    /** @var string */
    private $to;

    /** @var ParamHolderInterface */
    private $settings;

    /** @var ConstraintInterface[] */
    private $constraints;

    public function __construct(
        string $from,
        string $to,
        ParamHolderInterface $settings = null,
        array $constraints = []
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->settings = $settings ?? new Settings;
        $this->constraints = (function (ConstraintInterface ...$constraints) {
            return $constraints;
        })(...$constraints);
    }

    public function isActivatedBy(InputInterface $input, OutputInterface $output): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->accepts($input, $output)) {
                return false;
            }
        }
        return true;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getLabel(): string
    {
        return $this->settings->get('label') ?? '';
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function hasConstraints(): bool
    {
        return !empty($this->constraints);
    }

    public function __toString(): string
    {
        $label = implode("\nand ", $this->constraints);
        return empty($label) ? $this->getLabel() : $label;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $name, $default = null)
    {
        return $this->settings->get($name) ?? $default;
    }

    public function hasSetting(string $name): bool
    {
        return $this->settings->has($name);
    }

    public function getSettings(): ParamHolderInterface
    {
        return $this->settings;
    }
}
