<?php
function calculatePi(int $nTerms): float {
    $numerator = 4.0;
    $denominator = 1.0;
    $operation = 1;
    $pi = 0.0;
    
    for($counter = 0; $counter <= $nTerms; $counter++) {
        $pi += $operation * ($numerator / $denominator);
        $denominator += 2;
        $operation *= -1;
    }

    return $pi;
}

echo calculatePi(1000000) . "\n";
