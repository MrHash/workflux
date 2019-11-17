<?php

namespace Workflux\Error;

use DomainException;
use Workflux\Error\ErrorInterface;

class InvalidInput extends DomainException implements ErrorInterface
{
    /** @var string[] */
    private $validation_errors;

    public function __construct(array $validation_errors, string $msg = '')
    {
        $this->validation_errors = $validation_errors;
        parent::__construct($msg.PHP_EOL.$this);
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function __toString(): string
    {
        $errors = [];
        foreach ($this->getValidationErrors() as $prop_name => $errors) {
            $errors[] = $prop_name.': '.implode(', ', $errors);
        }
        return implode("\n", $errors);
    }
}
