<?php

require_once "../data_structures.php";
require_once "weighted_edge.php";
require_once "weighted_graph.php";

function totalWeight(TypedSequence $weightedPath)
{
    $sum = 0;

    /** @var WeightedEdge $edge */
    foreach ($weightedPath as $edge) {
        $sum += $edge->getWeight();
    }

    return $sum;
}

function mst(WeightedGraph $wg, int $start = 0): ?TypedSequence
{
    if ($start > $wg->vertexCount() - 1 || $start < 0) {
        return null;
    }

    $result = TypedSequence::forType(WeightedEdge::class);
    $pq = new \Ds\PriorityQueue();
    $visited = TypedMap::forType('boolean');

    visit($start, $visited, $wg, $pq);

    while(!$pq->isEmpty()) {
        /** @var WeightedEdge $edge */
        $edge = $pq->pop();

        if ($visited->offsetExists($edge->getV())) {
            continue;
        }

        $result->append($edge);
        visit($edge->getV(), $visited, $wg, $pq);
    }

    return $result;
}

function visit(int $index, TypedMap $visited, WeightedGraph $wg, \Ds\PriorityQueue &$pq)
{
    $visited[$index] = true;

    /** @var WeightedEdge $edge */
    foreach ($wg->edgesForIndex($index) as $edge) {
        if (!$visited->offsetExists($edge->getV())) {
            $pq->push($edge, $edge->getWeight() * -1);
        }
    }
}

function printWeightedGraph(WeightedGraph $wg, TypedSequence $wp)
{
    $result = "\n";

    /** @var WeightedEdge $edge */
    foreach ($wp as $edge) {
        $result .= "{$wg->vertexAt($edge->getU())} {$edge->getWeight()}> {$wg->vertexAt($edge->getV())} \n";
    }

    $result .= "\nTotal Weight: " . totalWeight($wp);

    return $result;
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

$result = mst($graph);

if (!$result) {
    echo "No solution found!";
} else {
    echo printWeightedGraph($graph, $result);
}