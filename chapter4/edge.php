<?php

class Edge
{
    /** from */
    protected int $u;

    /** to */
    protected int $v;

    public function __construct(int $u, int $v)
    {
        $this->u = $u;
        $this->v = $v;
    }

    public function reversed(): self
    {
        return new self($this->v, $this->u);
    }

    public function __toString(): string
    {
        return "{$this->u} -> {$this->v}";
    }

    /**
     * @return int
     */
    public function getU(): int
    {
        return $this->u;
    }

    /**
     * @return int
     */
    public function getV(): int
    {
        return $this->v;
    }
}