<?php

require_once "../data_structures.php";

abstract class Constraint
{
    protected Sequence $variables;

    public function __construct(Sequence $variables)
    {
        $this->variables = $variables;
    }

    abstract public function satisfied(Map $assignment): bool;

    public function getVariables(): Sequence
    {
        return $this->variables;
    }
}

class CSP
{
    private Sequence $variables;
    private TypedMap $domains;
    private TypedMap $constraints;

    public function __construct(Sequence $variables, TypedMap $domains)
    {
        $this->variables = $variables;
        $this->domains = $domains;
        $this->constraints = TypedMap::forType(Sequence::class);

        $this->populateConstraintsKeys($variables, $domains);
    }

    private function populateConstraintsKeys(Sequence $variables, TypedMap $domains): void
    {
        foreach ($variables as $variable) {
            $this->constraints[$variable] = new Sequence();

            if (!isset($domains[$variable])) {
                throw new InvalidArgumentException('Every variable should have a domain assigned to it.');
            }
        }
    }

    public function addConstraint(Constraint $constraint): void
    {
        foreach ($constraint->getVariables() as $variable) {
            if (!$this->variables->has($variable)) {
                throw new InvalidArgumentException("Variable in constraint not in CSP");
            }

            $this->constraints[$variable]->append($constraint);
        }
    }

    private function consistent($variable, Map $assignment): bool
    {
        foreach ($this->constraints[$variable] as $constraint) {
            /** @var Constraint $constraint */
            if (!$constraint->satisfied($assignment)) {
                return false;
            }
        }

        return true;
    }

    public function backtrackingSearch(Map $assignment = null): ?Map
    {
        $assignment = $assignment ?? new Map();

        if ($assignment->count() == $this->variables->count()) {
            return $assignment;
        }

        $unassigned = [];

        foreach ($this->variables as $variable) {
            if (!$assignment->offsetExists($variable)) {
                $unassigned[] = $variable;
            }
        }

        $first = $unassigned[0];

        foreach ($this->domains[$first] as $value) {
            $localAssignment = clone $assignment;
            $localAssignment[$first] = $value;

            if ($this->consistent($first, $localAssignment)) {
                $result = $this->backtrackingSearch($localAssignment);

                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }
}