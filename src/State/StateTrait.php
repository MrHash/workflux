<?php

namespace Workflux\State;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Workflux\Error\ConfigError;
use Workflux\Param\InputInterface;
use Workflux\Param\Output;
use Workflux\Param\OutputInterface;
use Workflux\Param\ParamHolderInterface;
use Workflux\State\ValidatorInterface;

trait StateTrait
{
    /** @var string */
    private $name;

    /** @var ParamHolderInterface */
    private $settings;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ExpressionLanguage */
    private $expression_engine;

    public function __construct(
        string $name,
        ParamHolderInterface $settings,
        ValidatorInterface $validator,
        ExpressionLanguage $expression_engine
    ) {
        $this->name = $name;
        $this->settings = $settings;
        $this->validator = $validator;
        $this->expression_engine = $expression_engine;
        foreach ($this->getRequiredSettings() as $setting_name) {
            if (!$this->settings->has($setting_name)) {
                throw new ConfigError("Trying to configure state '$name' without required setting '$setting_name'.");
            }
        }
    }

    public function execute(InputInterface $input): OutputInterface
    {
        $this->validator->validateInput($this, $input);
        $output = $this->generateOutput($input);
        $this->validator->validateOutput($this, $output);
        return $output;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isInitial(): bool
    {
        return false;
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function isInteractive(): bool
    {
        return false;
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $name, $default = null)
    {
        return $this->settings->get($name) ?? $default;
    }

    public function getSettings(): ParamHolderInterface
    {
        return $this->settings;
    }

    private function generateOutput(InputInterface $input): OutputInterface
    {
        return new Output(
            $this->name,
            array_merge(
                $this->evaluateInputExports($input),
                $this->generateOutputParams($input)
            )
        );
    }

    private function evaluateInputExports(InputInterface $input): array
    {
        $exports = [];
        foreach ($this->getSetting('_output', []) as $key => $value) {
            $exports[$key] = $this->expression_engine->evaluate($value, [ 'input' => $input ]);
        }
        return $exports;
    }

    private function generateOutputParams(InputInterface $input): array
    {
        return [];
    }

    private function getRequiredSettings(): array
    {
        return [];
    }
}
