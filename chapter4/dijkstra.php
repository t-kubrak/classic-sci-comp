<?php

require_once "../data_structures.php";
require_once "weighted_edge.php";
require_once "weighted_graph.php";
require_once "mst.php";

class DijkstraNode
{
    private int $vertex;
    private float $distance;

    public function __construct(int $vertex, float $distance)
    {
        $this->vertex = $vertex;
        $this->distance = $distance;
    }

    public function getVertex(): int
    {
        return $this->vertex;
    }
}

function dijkstra(WeightedGraph $wg, $root): array
{
    $first = $wg->indexOf($root);
    $distances = new Map();
    $distances[$first] = 0;
    $path = TypedMap::forType(WeightedEdge::class);
    $pq = new \Ds\PriorityQueue();
    $pq->push(new DijkstraNode($first, 0), 0);

    while (!$pq->isEmpty()) {
        $node = $pq->pop();
        /** @var DijkstraNode $node */
        $currentVertex = $node->getVertex();
        $distanceToCurrentVertex = $distances[$currentVertex];

        /** @var WeightedEdge $we */
        foreach ($wg->edgesForIndex($currentVertex) as $we) {
            $distanceToNeighbourVertex = $distances[$we->getV()];
            $newDistanceToNeighbourVertex = $we->getWeight() + $distanceToCurrentVertex;

            if (is_null($distanceToNeighbourVertex) || $distanceToNeighbourVertex > $newDistanceToNeighbourVertex) {
                $distances[$we->getV()] = $newDistanceToNeighbourVertex;
                $path[$we->getV()] = $we;
                $pq->push(
                    new DijkstraNode($we->getV(), $newDistanceToNeighbourVertex),
                    $newDistanceToNeighbourVertex * -1
                );
            }
        }
    }

    return [$distances, $path];
}

function vertexNameToDistanceFrom(WeightedGraph $wg, Map $distances): Map
{
    $distanceMap = new Map();

    foreach (range(0, $distances->count() - 1) as $i) {
        $distanceMap[$wg->vertexAt($i)] = $distances[$i];
    }

    return $distanceMap;
}

function pathMapToPath(int $start, int $end, TypedMap $path): ?TypedSequence
{
    if ($path->count() == 0) {
        return null;
    }

    $edgePath = TypedSequence::forType(WeightedEdge::class);
    /** @var WeightedEdge $e */
    $e = $path[$end];
    $edgePath[] = $e;

    while ($e->getU() != $start) {
        $e = $path[$e->getU()];
        $edgePath[] = $e;
    }

    return $edgePath->reversed();
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

[$distances, $path] = dijkstra($graph, "Los Angeles");
$nameDistance = vertexNameToDistanceFrom($graph, $distances);

echo "\nDistances from Los Angeles: ";

foreach ($nameDistance as $key => $value) {
    echo "\n{$key} : $value";
}

echo "\n\nShortest path from Los Angeles to Boston:";

$path = pathMapToPath($graph->indexOf("Los Angeles"), $graph->indexOf("Boston"), $path);

echo printWeightedGraph($graph, $path);