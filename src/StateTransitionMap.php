<?php

namespace Workflux;

use Ds\Map;
use Traversable;
use IteratorAggregate;

final class StateTransitionMap implements IteratorAggregate
{
    /**
     * @var Map $internal_map
     */
    private $internal_map;

    /**
     * @param TransitionInterface[] $states
     */
    public function __construct(array $transitions = [])
    {
        $this->internal_map = new Map;
        (function (TransitionInterface ...$transitions) {
            foreach ($transitions as $transition) {
                $state_transitions = $this->internal_map->get($transition->getFrom(), new TransitionSet);
                $this->internal_map->put($transition->getFrom(), $state_transitions->add($transition));
            }
        })(...$transitions);
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return self
     */
    public function put(TransitionInterface $transition): self
    {
        $cloned_map = clone $this;
        $cloned_map->internal_map->put(
            $transition->getFrom(),
            $cloned_map->get($transition->getFrom())
                ->add($transition)
        );

        return $cloned_map;
    }

    /**
     * @param string $state_name
     *
     * @return bool
     */
    public function has(string $state_name): bool
    {
        return $this->internal_map->hasKey($state_name);
    }

    /**
     * @param string $state_name
     *
     * @return TransitionSet
     */
    public function get(string $state_name): TransitionSet
    {
        return $this->internal_map->get($state_name, new TransitionSet);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->internal_map->count();
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return $this->internal_map->getIterator();
    }

    /**
     * @return StateInterface[]
     */
    public function toArray()
    {
        return $this->internal_map->toArray();
    }

    public function __clone()
    {
        $this->internal_map = clone $this->internal_map;
    }
}
