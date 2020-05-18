<?php

require_once "../functions.php";

$vtDistances = [
    "Rutland" => [
        "Burlington" => 67,
        "White River Junction" => 46,
        "Bennington" => 55,
        "Brattleboro" => 75
    ],
    "Burlington" => [
        "Rutland" => 67,
        "White River Junction" => 91,
        "Bennington" => 122,
        "Brattleboro" => 153
    ],
    "White River Junction" => [
        "Rutland" => 46,
        "Burlington" => 91,
        "Bennington" => 98,
        "Brattleboro" => 65
    ],
    "Bennington" => [
        "Rutland" => 55,
        "Burlington" => 122,
        "White River Junction" => 98,
        "Brattleboro" => 40
    ],
    "Brattleboro" => [
        "Rutland" => 75,
        "Burlington" => 153,
        "White River Junction" => 65,
        "Bennington" => 40
    ]
];

$vtCities = array_keys($vtDistances);
$cityPermutations = permutations($vtCities);
$tspPaths = [];

foreach ($cityPermutations as $key => $cities) {
    $tspPaths[] = array_merge($cities, [$cities[0]]);
}

$bestPath = [];
$minDistance = 99999999999; // arbitrarily high number

foreach ($tspPaths as $path) {
    $distance = 0;
    $last = $path[0];

    for ($i = 1; $i < count($path); $i++) {
        $next = $path[$i];
        $distance += $vtDistances[$last][$next];
        $last = $next;
    }

    if ($distance < $minDistance) {
        $minDistance = $distance;
        $bestPath = $path;
    }
}

$path = implode("->", $bestPath);

echo "The shortest path is {$path} in {$minDistance} miles.";