<?php

namespace Workflux\Param;

interface ParamHolderInterface
{
    /** @return mixed */
    public function get(string $param_name);

    public function has(string $param_name): bool;

    public function getParams(): array;

    /** @param mixed $param_value */
    public function withParam(string $param_name, $param_value, bool $treat_name_as_path = true): self;

    public function withParams(array $params): self;

    public function withoutParam(string $param_name): ParamHolderInterface;

    /** @param string[] $param_names */
    public function withoutParams(array $param_names): ParamHolderInterface;

    public function toArray(): array;
}
