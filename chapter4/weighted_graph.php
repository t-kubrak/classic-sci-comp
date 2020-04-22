<?php

require_once "weighted_edge.php";
require_once "graph.php";
require_once "../data_structures.php";

class WeightedGraph extends Graph
{
    public function __construct(TypedSequence $vertices)
    {
        parent::__construct($vertices, WeightedEdge::class);
    }

    public function addEdgeByIndices(int $u, int $v, float $weight = 0.0): void
    {
        $edge = new WeightedEdge($u, $v, $weight);
        $this->addEdge($edge);
    }

    public function addEdgeByVertices($first, $second, float $weight = 0.0): void
    {
        $u = $this->vertices->index($first);
        $v = $this->vertices->index($second);
        $this->addEdgeByIndices($u, $v, $weight);
    }

    public function neighborsForIndexWithWeights(int $index): Map
    {
        $distance = new Map();

        /** @var WeightedEdge $edge */
        foreach ($this->edgesForIndex($index) as $edge) {
            $distance->offsetSet($this->vertexAt($edge->getV()), $edge->getWeight());
        }

        return $distance;
    }

    public function __toString(): string
    {
        $desc = "";

        foreach (range(0, $this->vertices->count() - 1) as $index) {
            $neighborsMap = $this->neighborsForIndexWithWeights($index)->toArray();

            $neighborWeights = array_map(function ($key, $value) {
                return "({$key}, {$value})";
            }, array_keys($neighborsMap), $neighborsMap);

            $neighborWeights = implode(", ", $neighborWeights);

            $desc .= "{$this->vertexAt($index)} -> [{$neighborWeights}]\n";
        }

        return $desc;
    }
}

$cities = [
    "Seattle", "San Francisco", "Los Angeles", "Riverside", "Phoenix", "Chicago", "Boston", "New York",
    "Atlanta", "Miami", "Dallas", "Houston", "Detroit", "Philadelphia", "Washington"
];
$vertices = TypedSequence::forType('string');

foreach ($cities as $city) {
    $vertices->append($city);
}

$graph = new WeightedGraph($vertices);

$graph->addEdgeByVertices("Seattle", "Chicago", 1737);
$graph->addEdgeByVertices("Seattle", "San Francisco", 678);
$graph->addEdgeByVertices("San Francisco", "Riverside", 386);
$graph->addEdgeByVertices("San Francisco", "Los Angeles", 348);
$graph->addEdgeByVertices("Los Angeles", "Riverside", 50);
$graph->addEdgeByVertices("Los Angeles", "Phoenix", 357);
$graph->addEdgeByVertices("Riverside", "Phoenix", 307);
$graph->addEdgeByVertices("Riverside", "Chicago", 1704);
$graph->addEdgeByVertices("Phoenix", "Dallas", 887);
$graph->addEdgeByVertices("Phoenix", "Houston", 1015);
$graph->addEdgeByVertices("Dallas", "Chicago", 805);
$graph->addEdgeByVertices("Dallas", "Atlanta", 721);
$graph->addEdgeByVertices("Dallas", "Houston", 225);
$graph->addEdgeByVertices("Houston", "Atlanta", 702);
$graph->addEdgeByVertices("Houston", "Miami", 968);
$graph->addEdgeByVertices("Atlanta", "Chicago", 588);
$graph->addEdgeByVertices("Atlanta", "Washington", 543);
$graph->addEdgeByVertices("Atlanta", "Miami", 604);
$graph->addEdgeByVertices("Miami", "Washington", 923);
$graph->addEdgeByVertices("Chicago", "Detroit", 238);
$graph->addEdgeByVertices("Detroit", "Boston", 613);
$graph->addEdgeByVertices("Detroit", "Washington", 396);
$graph->addEdgeByVertices("Detroit", "New York", 482);
$graph->addEdgeByVertices("Boston", "New York", 190);
$graph->addEdgeByVertices("New York", "Philadelphia", 81);
$graph->addEdgeByVertices("Philadelphia", "Washington", 123);

echo "\n" . $graph;