<?php

class C4Piece implements Piece
{
    private const B = "B";
    private const R = "R";
    private const E = " "; //empty
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function opposite(): self
    {
        $opposite = clone $this;

        if ($this->value == self::B) {
            $opposite->value = self::B;
        } elseif ($this->value == self::R) {
            $opposite->value = self::R;
        } else {
            $opposite->value = self::E;
        }

        return $opposite;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

