<?php

namespace Workflux\Transition;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Workflux\Param\InputInterface;
use Workflux\Param\OutputInterface;

final class ExpressionConstraint implements ConstraintInterface
{
    /** @var string */
    private $expression;

    /** @var ExpressionLanguage */
    private $engine;

    public function __construct(string $expression, ExpressionLanguage $engine)
    {
        $this->expression = $expression;
        $this->engine = $engine;
    }

    public function accepts(InputInterface $input, OutputInterface $output): bool
    {
        return (bool)$this->engine->evaluate(
            $this->expression,
            [ 'event' => $input->getEvent(), 'input' => $input, 'output' => $output ]
        );
    }

    public function __toString(): string
    {
        return str_replace('and', "\nand", $this->expression);
    }
}
