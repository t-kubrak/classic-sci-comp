<?php

require_once "edge.php";
require_once "../data_structures.php";
require_once "../chapter2/generic_search.php";

class Graph
{
    /**
     * @var TypedSequence
     */
    protected TypedSequence $vertices;
    /**
     * @var TypedSequence
     */
    protected TypedSequence $edges;
    /**
     * @var string
     */
    protected string $edgeType;

    public function __construct(TypedSequence $vertices, string $edgeType = null)
    {
        $this->vertices = $vertices;

        $edgeType = $edgeType ?? Edge::class;
        $this->edges = $this->edgesFrom($vertices, $edgeType);
    }

    private function edgesFrom(TypedSequence $vertices, string $edgeType): TypedSequence
    {
        $edges = TypedSequence::forType(TypedSequence::class);

        foreach ($vertices as $vertex) {
            $edges->append(TypedSequence::forType($edgeType));
        }

        return $edges;
    }

    public function vertexCount(): int
    {
        return $this->vertices->count();
    }

    public function edgeCount(): int
    {
        $count = 0;

        /** @var TypedSequence $edgeList */
        foreach ($this->edges as $edgeList) {
            $count += $edgeList->count();
        }

        return $count;
    }

    /**
     * Add a vertex to the graph and return its index
     */
    public function addVertex($vertex): int
    {
        $this->vertices->append($vertex);
        $this->edges->append(TypedList::forType(Edge::class));

        return $this->vertices->count() - 1;
    }

    /**
     * This is an undirected graph,
     * so we always add edges in both directions
     */
    public function addEdge(Edge $edge): void
    {
        $this->edges[$edge->getU()]->append($edge);
        $this->edges[$edge->getV()]->append($edge->reversed());
    }

    public function addEdgeByIndices(int $u, int $v): void
    {
        $edge = new Edge($u, $v);
        $this->addEdge($edge);
    }

    public function addEdgeByVertices($first, $second): void
    {
        $u = $this->vertices->index($first);
        $v = $this->vertices->index($second);
        $this->addEdgeByIndices($u, $v);
    }

    public function vertexAt(int $index)
    {
        return $this->vertices[$index];
    }

    /**
     * Find the index of a vertex in the graph
     */
    public function indexOf($vertex): int
    {
        return $this->vertices->index($vertex);
    }

    /**
     * Find the vertices that a vertex at some index is connected to
     */
    public function neighborsForIndex(int $index): Sequence
    {
        $vertices = [];

        /** @var Edge $edge */
        foreach ($this->edges[$index] as $edge) {
            $vertices[] = $this->vertexAt($edge->getV());
        }

        return new Sequence($vertices);
    }

    /**
     * Lookup a vertice's index and find its neighbors
     */
    public function neighborsForVertex($vertex): Sequence
    {
        return $this->neighborsForIndex($this->indexOf($vertex));
    }

    public function edgesForIndex(int $index): TypedSequence
    {
        return $this->edges[$index];
    }

    public function edgesForVertex($vertex): TypedSequence
    {
        return $this->edgesForIndex($this->indexOf($vertex));
    }

    public function __toString(): string
    {
        $desc = "";

        foreach (range(0, $this->vertices->count() - 1) as $index) {
            $pieces = $this->neighborsForIndex($index)->toArray();
            $neighbors = implode(", ", $pieces);
            $desc .= "{$this->vertexAt($index)} -> [{$neighbors}]\n";
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

$graph = new Graph($vertices);

$graph->addEdgeByVertices("Seattle", "Chicago");
$graph->addEdgeByVertices("Seattle", "San Francisco");
$graph->addEdgeByVertices("San Francisco", "Riverside");
$graph->addEdgeByVertices("San Francisco", "Los Angeles");
$graph->addEdgeByVertices("Los Angeles", "Riverside");
$graph->addEdgeByVertices("Los Angeles", "Phoenix");
$graph->addEdgeByVertices("Riverside", "Phoenix");
$graph->addEdgeByVertices("Riverside", "Chicago");
$graph->addEdgeByVertices("Phoenix", "Dallas");
$graph->addEdgeByVertices("Phoenix", "Houston");
$graph->addEdgeByVertices("Dallas", "Chicago");
$graph->addEdgeByVertices("Dallas", "Atlanta");
$graph->addEdgeByVertices("Dallas", "Houston");
$graph->addEdgeByVertices("Houston", "Atlanta");
$graph->addEdgeByVertices("Houston", "Miami");
$graph->addEdgeByVertices("Atlanta", "Chicago");
$graph->addEdgeByVertices("Atlanta", "Washington");
$graph->addEdgeByVertices("Atlanta", "Miami");
$graph->addEdgeByVertices("Miami", "Washington");
$graph->addEdgeByVertices("Chicago", "Detroit");
$graph->addEdgeByVertices("Detroit", "Boston");
$graph->addEdgeByVertices("Detroit", "Washington");
$graph->addEdgeByVertices("Detroit", "New York");
$graph->addEdgeByVertices("Boston", "New York");
$graph->addEdgeByVertices("New York", "Philadelphia");
$graph->addEdgeByVertices("Philadelphia", "Washington");

echo $graph;

$bfsResult = bfs(
    "Boston",
    function ($city) {
        return $city == "Miami";
    },
    [$graph, 'neighborsForVertex']
);

if (!$bfsResult) {
    echo "No solution found using breadth-first search.";
} else {
    $path = nodeToPath($bfsResult);
    echo "\nPath from Boston to Miami:\n" . implode(", ", $path);
}