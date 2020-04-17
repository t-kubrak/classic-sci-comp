<?php

class Sequence extends ArrayObject
{
    protected array $input = [];

    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flags, $iterator_class);
        $this->input = $input;
    }

    public function toArray(): array
    {
        return $this->input;
    }

    public function has($otherValue): bool
    {
        foreach ($this->input as $value) {
            if ($value == $otherValue) {
                return true;
            }
        }

        return false;
    }
}

class TypedSequence extends ArrayObject
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
        if ((is_object($value) && !$value instanceof $this->type)) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        } elseif (!is_object($value) && $value != $this->type) {
            throw new TypeError("New value is not of type {$this->type}");
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

    public function has($otherValue): bool
    {
        foreach ($this->values as $value) {
            if ($value == $otherValue) {
                return true;
            }
        }

        return false;
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