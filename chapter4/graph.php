<?php

require_once "edge.php";
require_once "../data_structures.php";

class Graph
{
    /**
     * @var TypedSequence
     */
    private TypedSequence $vertices;
    /**
     * @var TypedSequence
     */
    private TypedSequence $edges;

    public function __construct(TypedSequence $vertices)
    {
        $this->vertices = $vertices;
        $this->edges = $this->edgesFrom($vertices);
    }

    private function edgesFrom(TypedSequence $vertices): TypedSequence
    {
        $edges = TypedSequence::forType(TypedSequence::class);

        foreach ($vertices as $vertex) {
            $edges->append(TypedSequence::forType(Edge::class));
        }

        return $edges;
    }

    public function vertedCount(): int
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
        $this->vertices->add($vertex);
        $this->edges->add(TypedList::forType(Edge::class));

        return $this->vertices->count() - 1;
    }

    /**
     * This is an undirected graph,
     * so we always add edges in both directions
     */
    public function addEdge(Edge $edge): void
    {
        $this->edges[$edge->getU()]->add($edge);
        $this->edges[$edge->getV()]->add($edge->reversed());
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
    public function neighborsForIndex(int $index): ArrayObject
    {
        $vertices = new ArrayObject();

        /** @var Edge $edge */
        foreach ($this->edges[$index] as $edge) {
            $vertices->append($edge->getV());
        }

        return $vertices;
    }

    /**
     * Lookup a vertice's index and find its neighbors
     */
    public function neighborsForVertex($vertex)
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
            $desc .= "{$this->vertexAt($index)} -> {$this->neighborsForIndex($index)}\n";
        }

        return $desc;
    }
}