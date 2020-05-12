<?php

require_once "../data_structures.php";
require_once "data_point.php";
require_once "kmeans.php";

class Album extends DataPoint
{
    private string $name;
    private int $year;
    private float $length;
    private float $tracks;

    public function __construct(string $name, int $year, float $length, float $tracks)
    {
        parent::__construct(new Sequence([$length, $tracks]));

        $this->name = $name;
        $this->year = $year;
        $this->length = $length;
        $this->tracks = $tracks;
    }

    public function __toString(): string
    {
        return "{$this->name}, {$this->year}";
    }
}

$data = [new Album("Got to Be There", 1972, 35.45, 10),
    new Album("Ben", 1972, 31.31, 10),
    new Album("Music & Me", 1973, 32.09, 10),
    new Album("Forever, Michael", 1975, 33.36, 10),
    new Album("Off the Wall", 1979, 42.28, 10),
    new Album("Thriller", 1982, 42.19, 9),
    new Album("Bad", 1987, 48.16, 10),
    new Album("Dangerous", 1991, 77.03, 14),
    new Album("HIStory: Past, Present and Future, Book I", 1995, 148.58, 30),
    new Album("Invincible", 2001, 77.05, 16)];

$albums = TypedSequence::forType(Album::class);

foreach ($data as $album) {
    $albums->append($album);
}

$kmeans = new KMeans(2, $albums);
$clusters = $kmeans->run();

foreach ($clusters as $index => $cluster) {
    $centroidDimensions = $cluster->getCentroid()->getDimensions();
    echo "Cluster {$index} Avg Length " . $centroidDimensions[0]
        . " Avg Tracks " . $centroidDimensions[1] . ": {$cluster}\n";
}