<?php

require_once "../functions.php";

$phoneMapping = [
     "1" => ["1"],
     "2" => ["a", "b", "c"],
     "3" => ["d", "e", "f"],
     "4" => ["g", "h", "i"],
     "5" => ["j", "k", "l"],
     "6" => ["m", "n", "o"],
     "7" => ["p", "q", "r", "s"],
     "8" => ["t", "u", "v"],
     "9" => ["w", "x", "y", "z"],
     "0" => ["0"]
];

function possibleMnemonics(string $phoneNumber, array $phoneMapping)
{
    $letterLists = [];

    foreach (str_split($phoneNumber) as $digit) {
        $letterLists[] = $phoneMapping[$digit];
    }

    return cartesian($letterLists);
}

$phoneNumber = readline("Enter a phone number:");

echo "Here are the potential mnemonics:". PHP_EOL;

$possibleMnemonics = possibleMnemonics($phoneNumber, $phoneMapping);

foreach ($possibleMnemonics as $mnemonic) {
    echo implode("", $mnemonic) . PHP_EOL;
}

