<?php

class Sequence implements ArrayAccess, Countable, IteratorAggregate
{
    protected array $values;

    public function __construct($values = [])
    {
        $this->values = $values;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function append($value): self
    {
        $this->values[] = $value;
        return $this;
    }

    public function merge(array $values): self
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }

    public function pop()
    {
        $lastKey = $this->count() - 1;

        $lastValue = $this->values[$lastKey];

        unset($this->values[$lastKey]);

        return $lastValue;
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

    public function has($otherValue): bool
    {
        foreach ($this->values as $value) {
            if ($value == $otherValue) {
                return true;
            }
        }

        return false;
    }

    public function index($otherValue)
    {
        foreach ($this->values as $key => $value) {
            if ($value == $otherValue) {
                return $key;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function reversed()
    {
        return new self(array_reverse($this->values));
    }

    public function min()
    {
        return min($this->values);
    }

    public function max()
    {
        return max($this->values);
    }


    /**
     * @return float|int
     */
    public function sum()
    {
        return array_sum($this->values);
    }

    /**
     * @return float|int
     */
    public function mean()
    {
        return $this->sum() / $this->count();
    }

    public function pstDev(): float
    {
        $mean = $this->mean();

        $deviations = array_map(function($value) use ($mean) {
            return ($value - $mean) ** 2;
        }, $this->values);

        return sqrt(array_sum($deviations) / $this->count());
    }

    public function zip(Sequence $otherValues): Sequence
    {
        $deviations = array_map(function($value, $otherValue) {
            return [$value, $otherValue];
        }, $this->values, $otherValues->toArray());

        return new Sequence($deviations);
    }
}

class TypedSequence extends Sequence
{
    protected string $type;

    public static function forType(string $type): self
    {
        $list = new static();
        $list->type = $type;
        return $list;
    }

    protected function __construct()
    {
    }

    public function append($value): self
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
        if ((is_object($value) && !$value instanceof $this->type)) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        } elseif (!is_object($value) && gettype($value) != $this->type) {
            throw new TypeError("New value is not of type {$this->type}");
        }
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

    public function reversed(): self
    {
        $self = clone $this;
        $self->values = array_reverse($self->values);

        return $self;
    }
}

class Map implements ArrayAccess, Countable, IteratorAggregate
{
    protected array $values = [];
    protected $default;

    public function __construct($default = null)
    {
        $this->default = $default;
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
        return $this->values[$offset] ?? $this->default;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->values[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }

    public function has($otherValue): bool
    {
        foreach ($this->values as $value) {
            if ($value == $otherValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    public function toArray(): array
    {
        return $this->values;
    }
}

class TypedMap extends Map
{
    protected string $type;
    protected array $values = [];
    protected $default;

    public static function forType(string $type, $default = null): self
    {
        $list = new static();
        $list->type = $type;
        $list->default = $default;
        return $list;
    }

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $value
     */
    private function validate($value): void
    {
        if ((is_object($value) && !$value instanceof $this->type)) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        } elseif (!is_object($value) && gettype($value) != $this->type) {
            throw new TypeError("New value is not of type {$this->type}");
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->validate($value);
        $this->values[$offset] = $value;
    }
}

class Stack
{
    private array $values;

    public function push($value): void
    {
        $this->values[] = $value;
    }

    public function pop()
    {
        return array_pop($this->values);
    }

    public function isEmpty(): bool
    {
        return count($this->values) < 1;
    }
}

class Queue
{
    private array $values;

    public function push($value): void
    {
        $this->values[] = $value;
    }

    public function pop()
    {
        return array_shift($this->values);
    }

    public function isEmpty(): bool
    {
        return count($this->values) < 1;
    }
}

/**
 * This could be be used instead of the standard one to enforce the order
 * of items with the same priority
 */
class PriorityQueue extends \SplPriorityQueue
{
    protected $queueOrder = PHP_INT_MAX;

    public function insert($datum, $priority)
    {
        if (is_int($priority) || is_float($priority)) {
            $priority = array($priority, $this->queueOrder--);
        }

        parent::insert($datum, $priority);
    }
}