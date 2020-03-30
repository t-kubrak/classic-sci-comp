<?php

class Sequence extends ArrayObject
{
    protected string $type;
    protected array $values = [];

    public static function forType(string $type): self
    {
        $list = new static();
        $list->type = $type;
        return $list;
    }

    protected function __construct()
    {
        parent::__construct();
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function add($value): self
    {
        $this->validate($value);

        $this->values[] = $value;
        return $this;
    }

    /**
     * @param $value
     */
    public function validate($value): void
    {
        if (!$value instanceof $this->type) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }

    public function sort(): bool
    {
        return sort($this->values);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->validate($value);

        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}

function linear_contains(ArrayObject $sequence, $key): bool
{
    foreach ($sequence as $item) {
        if ($item == $key) {
            return true;
        }
    }

    return false;
}

function binary_contains(ArrayObject $sequence, $key): bool
{
    $low = 0;
    $high = count($sequence) - 1;

    while ($low <= $high) {
        $mid = intdiv(($low + $high), 2);

        if ($key > $sequence[$mid]) {
            $low = $mid + 1;
        } elseif ($key < $sequence[$mid]) {
            $high = $mid - 1;
        } else {
            return true;
        }
    }

    return false;
}

var_dump(linear_contains(new ArrayObject([1, 5, 15, 15, 15, 20]), 5));
var_dump(binary_contains(new ArrayObject(['a', 'd', 'e', 'f', 'z']), 'f'));
var_dump(binary_contains(new ArrayObject(['john', 'mark', 'ronald', 'sarah']), 'sheila'));

