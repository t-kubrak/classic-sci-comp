<?php

require_once "../data_structures.php";
require_once "data_point.php";

function zscores(Sequence $original)
{
    $avg = $original->mean();
    $std = $original->pstDev();

    if ($std == 0) {
        return array_map(function(){
            return 0;
        }, $original->toArray());
    }

    return  array_map(function ($x) use ($std, $avg) {
        return ($x - $avg) / $std;
    }, $original->toArray());
}

class Cluster
{
    /** @var TypedSequence|DataPoint[] */
    private TypedSequence $points;
    private DataPoint $centroid;

    public function __construct(TypedSequence $points, DataPoint $centroid)
    {
        $this->points = $points;
        $this->centroid = $centroid;
    }

    public function getCentroid(): DataPoint
    {
        return $this->centroid;
    }

    /**
     * @return TypedSequence|DataPoint[]
     */
    public function getPoints(): TypedSequence
    {
        return $this->points;
    }

    public function addPoint(DataPoint $point): void
    {
        $this->points->append($point);
    }

    public function setCentroid(DataPoint $centroid): void
    {
        $this->centroid = $centroid;
    }

    public function clearPoints(): void
    {
        $this->points = TypedSequence::forType(DataPoint::class);
    }

    public function __toString(): string
    {
        $points = implode("), (", $this->getPoints()->toArray());
        return "[({$points})]";
    }
}

class KMeans
{
    /** @var TypedSequence|DataPoint[] */
    private TypedSequence $points;

    /** @var TypedSequence|Cluster[] */
    private TypedSequence $clusters;

    public function __construct(int $k, TypedSequence $points)
    {
        if ($k < 1) {
            throw new InvalidArgumentException("k must be >= 1");
        }

        $this->points = $points;
        $this->zscoreNormalize();

        $this->clusters = TypedSequence::forType(Cluster::class);

        foreach (range(0, $k -1) as $value) {
            $randPoint = $this->randomPoint();
            $cluster = new Cluster(TypedSequence::forType(DataPoint::class), $randPoint);
            $this->clusters->append($cluster);
        }
    }

    public function centroids(): TypedSequence
    {
        $centroids = TypedSequence::forType(DataPoint::class);

        foreach ($this->clusters as $cluster) {
            $centroids->append($cluster->getCentroid());
        }

        return $centroids;
    }

    public function dimensionSlice(int $dimension): Sequence
    {
        $dimensions = new Sequence();

        foreach ($this->points as $x) {
            $value = $x->getDimensions()[$dimension];
            $dimensions->append($value);
        }

        return $dimensions;
    }

    private function zscoreNormalize(): void
    {
        $zscored = new Sequence([]);

        foreach (range(0, $this->points->count() - 1) as $item) {
            $zscored->append(new Sequence());
        }

        foreach (range(0, $this->points[0]->numDimensions() - 1) as $dimension) {
            $dimensionSlice = $this->dimensionSlice($dimension);

            foreach (zscores($dimensionSlice) as $index => $zscore) {
                $zscored[$index]->append($zscore);
            }
        }

        foreach (range(0, $this->points->count() - 1) as $i) {
            $this->points[$i]->setDimensions($zscored[$i]);
        }
    }

    private function randomPoint(): DataPoint
    {
        $randDimensions = new Sequence();

        foreach (range(0, $this->points[0]->numDimensions() - 1) as $dimension) {
            $values = $this->dimensionSlice($dimension);
            $randValue = rand($values->min(), $values->max());
            $randDimensions->append($randValue);
        }

        return new DataPoint($randDimensions);
    }

    /**
     * Find the closest cluster centroid to each point
     * and assign the point to that cluster
     */
    public function assignClusters(): void
    {
        $centroids = $this->centroids();

        foreach ($this->points as $point) {
            $distances = array_map(function (DataPoint $centroid) use ($point) {
                return $centroid->distance($point);
            }, $centroids->toArray());

            $closestKey = array_keys($distances, min($distances))[0];
            $closest = $centroids->offsetGet($closestKey);

            $idx = $centroids->index($closest);
            $cluster = $this->clusters[$idx];
            $cluster->addPoint($point);
        }
    }

    /**
     * Find the center of each cluster and move the centroid to there
     */
    public function generateCentroids(): void
    {
        foreach ($this->clusters as $cluster){
            if ($cluster->getPoints()->count() == 0) {
                continue; //keep the same centroid if no points
            }

            $means = new Sequence();

            foreach (range(0, $cluster->getPoints()[0]->numDimensions() - 1) as $dimension) {
                $dimensionSlice = array_map(function($p) use ($dimension) {
                    return $p->getDimensions()[$dimension];
                }, $cluster->getPoints()->toArray());

                $means->append((new Sequence($dimensionSlice))->mean());
            }

            $cluster->setCentroid(new DataPoint($means));
        }
    }

    /**
     * @param int $maxIterations
     * @return TypedSequence|Cluster[]
     */
    public function run(int $maxIterations = 100): TypedSequence
    {
        foreach (range(0, $maxIterations - 1) as $iteration) {
            foreach ($this->clusters as $cluster) {
                $cluster->clearPoints(); // clear all clusters
            }

            $this->assignClusters(); // find cluster each point is closest to

            $oldCentroids = clone $this->centroids(); // record

            $this->generateCentroids(); // find new centroids

            // have centroids moved ?
            if ($oldCentroids == $this->centroids()) {
                echo "Converged after {$iteration} iterations\n";
                return $this->clusters;
            }
        }

        return $this->clusters;
    }
}

$point1 = new DataPoint(new Sequence([2.0, 1.0, 1.0]));
$point2 = new DataPoint(new Sequence([2.0, 2.0, 5.0]));
$point3 = new DataPoint(new Sequence([3.0, 1.5, 2.5]));

$points = TypedSequence::forType(DataPoint::class);

$points->append($point1);
$points->append($point2);
$points->append($point3);

$kmeansTest = new KMeans(2, $points);
$testClusters = $kmeansTest->run();

foreach ($testClusters as $index => $cluster) {
    echo "Cluster {$index}: {$cluster} \n";
}