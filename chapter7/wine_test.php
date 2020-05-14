<?php

require_once "../data_structures.php";
require_once "network.php";

$wineParameters = new Sequence();
$wineClassifications = new Sequence();
$wineSpecies = TypedSequence::forType('integer');

$wines = array_map('str_getcsv', file('wine.csv'));
shuffle($wines);

foreach ($wines as $wine) {
    $parameters = array_slice($wine, 1, 14);
    $parameters = new Sequence(array_map(fn($n) => floatval($n), $parameters));

    $wineParameters->append($parameters);

    $species = intval($wine[0]);

    if ($species == 1) {
        $wineClassifications->append([1.0, 0.0, 0.0]);
    } elseif ($species == 2) {
        $wineClassifications->append([0.0, 1.0, 0.0]);
    } else {
        $wineClassifications->append([0.0, 0.0, 1.0]);
    }

    $wineSpecies->append($species);
}

normalizeByFeatureScaling($wineParameters);

$layerStructure = new Sequence([13, 7, 3]);

$wineNetwork = new Network($layerStructure, 0.9);

function wineInterpretOutput(Sequence $output): string
{
    if ($output->max() == $output[0]) {
        return 1;
    } elseif ($output->max() == $output[1]) {
        return 2;
    } else {
        return 3;
    }
}

$wineTrainers = new Sequence(array_slice($wineParameters->toArray(), 0, 150));
$wineTrainersCorrect = new Sequence(array_slice($wineClassifications->toArray(), 0, 150));

// train over the first 140 wines in the data set 50 times
foreach (range(1, 10) as $step) {
    $wineNetwork->train($wineTrainers, $wineTrainersCorrect);
}

// test over the last 10 of the wines in the data set
$wineTesters = new Sequence(array_slice($wineParameters->toArray(), 150, 178));
$wineTestersCorrect = new Sequence(array_slice($wineSpecies->toArray(), 150, 178));
$wineResults = $wineNetwork->validate($wineTesters, $wineTestersCorrect, 'wineInterpretOutput');

$percentage = $wineResults[2] * 100;
echo "{$wineResults[0]} correct of {$wineResults[1]} = {$percentage}%";
