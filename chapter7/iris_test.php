<?php

require_once "../data_structures.php";
require_once "network.php";

$irisParameters = new Sequence();
$irisClassifications = new Sequence();
$irisSpecies = TypedSequence::forType('string');

$irises = array_map('str_getcsv', file('iris.csv'));
shuffle($irises);

foreach ($irises as $iris) {
    $parameters = array_slice($iris, 0, 3);
    $parameters = new Sequence(array_map(fn($n) => floatval($n), $parameters));

    $irisParameters->append($parameters);

    $species = $iris[4];

    if ($species == "Iris-setosa") {
        $irisClassifications->append([1.0, 0.0, 0.0]);
    } elseif ($species == "Iris-versicolor") {
        $irisClassifications->append([0.0, 1.0, 0.0]);
    } else {
        $irisClassifications->append([0.0, 0.0, 1.0]);
    }

    $irisSpecies->append($species);
}

normalizeByFeatureScaling($irisParameters);

$layerStructure = new Sequence([4, 6, 3]);

$irisNetwork = new Network($layerStructure, 0.3);

function irisInterpretOutput(Sequence $output): string
{
    if ($output->max() == $output[0]) {
        return "Iris-setosa";
    } elseif ($output->max() == $output[1]) {
        return "Iris-versicolor";
    } else {
        return "Iris-virginica";
    }
}

$irisTrainers = new Sequence(array_slice($irisParameters->toArray(), 0, 140));
$irisTrainersCorrect = new Sequence(array_slice($irisClassifications->toArray(), 0, 140));

// train over the first 140 irises in the data set 50 times
foreach (range(1, 50) as $step) {
    $irisNetwork->train($irisTrainers, $irisTrainersCorrect);
}

// test over the last 10 of the irises in the data set
$irisTesters = new Sequence(array_slice($irisParameters->toArray(), 140, 10));
$irisTestersCorrect = new Sequence(array_slice($irisSpecies->toArray(), 140, 10));
$irisResults = $irisNetwork->validate($irisTesters, $irisTestersCorrect, 'irisInterpretOutput');

$percentage = $irisResults[2] * 100;
echo "{$irisResults[0]} correct of {$irisResults[1]} = {$percentage}%";
