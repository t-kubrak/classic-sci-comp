<?php

class Sequence extends ArrayObject {}

class Stack
{
    private array $values;

    public function __construct()
    {

    }

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

//var_dump(linear_contains(new ArrayObject([1, 5, 15, 15, 15, 20]), 5));
//var_dump(binary_contains(new ArrayObject(['a', 'd', 'e', 'f', 'z']), 'f'));
//var_dump(binary_contains(new ArrayObject(['john', 'mark', 'ronald', 'sarah']), 'sheila'));

class Node
{
    /**
     * @var object
     */
    private object $state;
    /**
     * @var Node
     */
    private ?Node $parent;
    /**
     * @var float
     */
    private float $cost;
    /**
     * @var float
     */
    private float $heuristic;

    public function __construct(object $state, Node $parent = null, float $cost = 0.0, float $heuristic = 0.0)
    {
        $this->state = $state;
        $this->parent = $parent;
        $this->cost = $cost;
        $this->heuristic = $heuristic;
    }

    public function lessThan(Node $other): bool
    {
        return ($this->cost + $this->heuristic) < ($other->cost && $other->heuristic);
    }

    public function priority(): int
    {
        return ($this->cost + $this->heuristic) * -1;
    }

    public function cost(): float
    {
        return $this->cost;
    }

    public function heuristic(): float
    {
        return $this->heuristic;
    }

    /**
     * @return object
     */
    public function state(): object
    {
        return $this->state;
    }

    public function parent(): ?Node
    {
        return $this->parent;
    }
}

function dfs(object $initial, callable $goalTest, callable $successors): ?Node
{
    $frontier = new Stack();
    $frontier->push(new Node($initial));
    $explored = TypedSequence::forType(get_class($initial));
    $explored->add($initial);

    while (!$frontier->isEmpty()) {
        $currentNode = $frontier->pop();
        $currentState = $currentNode->state();

        if ($goalTest($currentState)) {
            return $currentNode;
        }

        foreach ($successors($currentState) as $child) {
            if ($explored->has($child)) {
                continue;
            }

            $explored->add($child);
            $frontier->push(new Node($child, $currentNode));
        }
    }

    return null;
}

/**
 * @param Node $node
 * @return Node[]
 */
function nodeToPath(Node $node): array
{
    $path = [$node->state()];

    while (!is_null($node->parent())) {
        $node = $node->parent();
        $path[] = $node->state();
    }

    return array_reverse($path);
}

class Queue
{
    private array $values;

    public function __construct()
    {

    }

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

function bfs(object $initial, callable $goalTest, callable $successors): ?Node
{
    $frontier = new Queue();
    $frontier->push(new Node($initial));
    $explored = TypedSequence::forType(get_class($initial));
    $explored->add($initial);

    while (!$frontier->isEmpty()) {
        $currentNode = $frontier->pop();
        $currentState = $currentNode->state();

        if ($goalTest($currentState)) {
            return $currentNode;
        }

        foreach ($successors($currentState) as $child) {
            if ($explored->has($child)) {
                continue;
            }

            $explored->add($child);
            $frontier->push(new Node($child, $currentNode));
        }
    }

    return null;
}

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

class Explored
{
    /**
     * @var object
     */
    private object $value;
    /**
     * @var float
     */
    private float $cost;

    public function __construct(object $value, float $cost)
    {
        $this->value = $value;
        $this->cost = $cost;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return object
     */
    public function getValue(): object
    {
        return $this->value;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }
}

class ExploredCollection // TODO: utilize keys
{
    /**
     * @var Explored[]
     */
    private array $explored;

    public function add(object $value, float $cost)
    {
        $this->explored[] = new Explored($value, $cost);
    }

    public function get(object $otherValue): ?Explored
    {
        foreach ($this->explored as $explored) {
            if ($explored->getValue() == $otherValue) {
                return $explored;
            }
        }

        return null;
    }

    public function set(object $otherValue, float $cost): void
    {
        foreach ($this->explored as $key => $explored) {
            if ($explored->getValue() == $otherValue) {
                $this->explored[$key] = new Explored($otherValue, $cost);
                return;
            }
        }

        $this->add($otherValue, $cost);
    }
}

function astar(object $initial, callable $goalTest, callable $successors, callable $heuristic): ?Node
{
    $frontier = new SplPriorityQueue();
    $initialNode = new Node($initial, null, 0.0, $heuristic($initial));
    $frontier->insert($initialNode, $initialNode->priority());
    $explored = new ExploredCollection();
    $explored->add($initial, 0.0);

    while (!$frontier->isEmpty()) {
        /** @var Node $currentNode */
        $currentNode = $frontier->extract();
        $currentState = $currentNode->state();

        if ($goalTest($currentState)) {
            return $currentNode;
        }

        foreach ($successors($currentState) as $child) {
            $newCost = $currentNode->cost() + 1;
            $exploredNode = $explored->get($child);

            if (!$exploredNode || $exploredNode->getCost() > $newCost) {
                $explored->set($child, $newCost);
                $node = new Node($child, $currentNode, $newCost, $heuristic($child));
                $frontier->insert($node, $node->priority());
            }
        }
    }

    return null;
}