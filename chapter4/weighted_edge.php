<?php

require_once "edge.php";

class WeightedEdge extends Edge
{
    private float $weight;

    public function __construct(int $u, int $v, float $weight)
    {
        parent::__construct($u, $v);

        $this->weight = $weight;
    }

    public function reversed(): self
    {
        return new self($this->v, $this->u, $this->weight);
    }

    public function __toString(): string
    {
        return "{$this->u} {$this->weight}> {$this->v}";
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
}