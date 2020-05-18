<?php

require_once "../data_structures.php";

class Item
{
    private string $name;
    private int $weight;
    private float $value;

    public function __construct(string $name, int $weight, float $value)
    {
        $this->name = $name;
        $this->weight = $weight;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}

/**
 * @param Item[]|TypedSequence $items
 * @param int $maxCapacity
 * @return TypedSequence
 */
function knapsack(TypedSequence $items, int $maxCapacity): TypedSequence
{
    // build up dynamic programming table
    $table = [];

    for ($i = 0; $i <= $items->count(); $i++) {
        for ($j = 0; $j <= $maxCapacity; $j++) {
            $table[$i][$j] = 0;
        }
    }

    foreach ($items as $i => $item) {
        foreach (range(1, $maxCapacity) as $capacity) {
            $previousItemsValue = $table[$i][$capacity];

            // item fits iin knapsack
            if ($capacity >= $item->getWeight()) {
                $valueFreeingWeightForItem = $table[$i][$capacity - $item->getWeight()];
                //only take if more valuable than previous item
                $table[$i + 1][$capacity] = max(
                    $valueFreeingWeightForItem + $item->getValue(),
                    $previousItemsValue
                );
            } else {
                $table[$i + 1][$capacity] = $previousItemsValue;
            }
        }
    }

    // figure out solution from table
    $solution = TypedSequence::forType(Item::class);
    $capacity = $maxCapacity;

    foreach (range($items->count(), 1, -1) as $i) {
        // was this item used?
        if ($table[$i - 1][$capacity] != $table[$i][$capacity]) {
            $solution->append($items[$i - 1]);
            // if the item was used, remove its weight
            $capacity -= $items[$i - 1]->getWeight();
        }
    }

    return $solution;
}

$items = TypedSequence::forType(Item::class)
    ->append(new Item("television", 50, 500))
    ->append(new Item("candlesticks", 2, 300))
    ->append(new Item("stereo", 35, 400))
    ->append(new Item("laptop", 3, 1000))
    ->append(new Item("food", 15, 50))
    ->append(new Item("clothing", 20, 800))
    ->append(new Item("jewelry", 1, 4000))
    ->append(new Item("books", 100, 300))
    ->append(new Item("printer", 18, 30))
    ->append(new Item("refrigerator", 200, 700))
    ->append(new Item("painting", 10, 1000));

var_dump(knapsack($items, 75));