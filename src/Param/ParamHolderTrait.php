<?php

namespace Workflux\Param;

use Workflux\Param\ParamHolderInterface;

trait ParamHolderTrait
{
    /** @var array */
    private $params = [];

    /** @param string $param_name */
    public function __get($param_name)
    {
        return $this->get($param_name);
    }

    /** @return mixed */
    public function get(string $param_name, bool $treat_name_as_path = true)
    {
        if (!$treat_name_as_path) {
            return $this->has($param_name) ? $this->params[$param_name] : null;
        }
        $params = $this->params;
        $name_parts = array_reverse(explode('.', $param_name));
        $cur_val = &$params;
        while (count($name_parts) > 1 && $cur_name = array_pop($name_parts)) {
            if (!array_key_exists($cur_name, $cur_val)) {
                return null;
            }
            $cur_val = &$cur_val[$cur_name];
        }
        return array_key_exists($name_parts[0], $cur_val) ? $cur_val[$name_parts[0]] : null;
    }

    public function has(string $param_name): bool
    {
        return array_key_exists($param_name, $this->params);
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function withParam(string $param_name, $param_value, bool $treat_name_as_path = true): ParamHolderInterface
    {
        $param_holder = clone $this;
        if ($treat_name_as_path) {
            $name_parts = array_reverse(explode('.', $param_name));
            $cur_val = &$param_holder->params;
            while (count($name_parts) > 1 && $cur_name = array_pop($name_parts)) {
                if (!isset($cur_val[$cur_name])) {
                    $cur_val[$cur_name] = [];
                }
                $cur_val = &$cur_val[$cur_name];
            }
            $cur_val[$name_parts[0]] = $param_value;
            return $param_holder;
        }

        $param_holder->params[$param_name] = $param_value;
        return $param_holder;
    }

    public function withParams(array $params): ParamHolderInterface
    {
        $param_holder = clone $this;
        $param_holder->params = array_merge($param_holder->params, $params);
        return $param_holder;
    }

    public function withoutParam(string $param_name): ParamHolderInterface
    {
        if (!$this->has($param_name)) {
            return $this;
        }
        $param_holder = clone $this;
        unset($param_holder->params[$param_name]);
        return $param_holder;
    }

    /** @param string[] $param_names */
    public function withoutParams(array $param_names): ParamHolderInterface
    {
        return array_reduce(
            $param_names,
            function (ParamHolderInterface $param_holder, string $param_name): ParamHolderInterface {
                return $param_holder->withoutParam($param_name);
            },
            $this
        );
    }

    public function toArray(): array
    {
        return $this->params;
    }
}
