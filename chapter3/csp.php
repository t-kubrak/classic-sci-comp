<?php

require_once "../data_structures.php";

abstract class Constraint
{
    protected ArrayObject $variables;

    public function __construct(ArrayObject $variables)
    {
        $this->variables = $variables;
    }

    abstract public function satisfied(Map $assignment): bool;
}

class CSP
{
    private Sequence $variables;
    private Map $domains;
    /**
     * @var Map|Constraint[]
     */
    private Map $constraints;

    public function __construct(Sequence $variables, Map $domains)
    {
        $this->variables = $variables;
        $this->domains = $domains;

        //$firstVar = $variables[0];
        //$varType = is_object($firstVar) ? get_class($firstVar) : gettype($firstVar);
        $this->constraints = Map::forType(Constraint::class);

        $this->populateConstraintsKeys($variables, $domains);
    }

    private function populateConstraintsKeys(ArrayObject $variables, Map $domains): void
    {
        foreach ($variables as $variable) {
            $this->constraints[$variable] = [];

            if (!isset($domains[$variable])) {
                throw new InvalidArgumentException('Every variable should have a domain assigned to it.');
            }
        }
    }

    private function consistent($variable, Map $assignment): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->satisfied($assignment)) {
                return false;
            }
        }

        return true;
    }

    public function backtrackingSearch(Map $assignment): ?Map
    {
        if ($assignment->count() == $this->variables->count()) {
            return $assignment;
        }

        $unassigned = array_filter($this->variables->toArray(), function ($var) {
            return !isset($assignment[$var]);
        });

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