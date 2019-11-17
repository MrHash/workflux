<?php

namespace Workflux\Param;

use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;
use Workflux\Param\ParamHolderTrait;

final class Input implements InputInterface
{
    use ParamHolderTrait;

    /** @var string */
    private $event;

    public function __construct(array $params = [], string $event = '')
    {
        $this->params = $params;
        $this->event = $event;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function hasEvent(): bool
    {
        return !empty($this->event);
    }

    public function withEvent(string $event): InputInterface
    {
        $clone = clone $this;
        $clone->event = $event;
        return $clone;
    }

    public static function fromOutput(OutputInterface $output): InputInterface
    {
        return new static($output->toArray()['params']);
    }
}
