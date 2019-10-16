<?php

/**
 * @param string $original
 * @return string[]
 * @throws Exception
 */
function encrypt(string $original): array
{
    $original = utf8_encode($original);
    $dummy = randomKey(strlen($original));

    $original = str_split($original);
    $encrypted = "";

    foreach ($original as $key => $originalChar) {
        $dummyChar = $dummy[$key];
        $encrypted .= $originalChar ^ $dummyChar;
    }

    return [$dummy, $encrypted];
}

/**
 * @param int $length
 * @return string
 * @throws Exception
 */
function randomKey(int $length): string
{
    $bytes = random_bytes($length);
    return $bytes;
}

/**
 * @param string $key1
 * @param string $key2
 * @return string
 */
function decrypt(string $key1, string $key2)
{
    $key1 = str_split($key1);
    $decrypted = "";

    foreach ($key1 as $i => $key1Char) {
        $key2Char = $key2[$i];
        $decrypted .= $key1Char ^ $key2Char;
    }

    return $decrypted;
}

$original = "Very original!";

[$key1, $key2] = encrypt($original);

$decrypted = decrypt($key1, $key2);

var_dump($original === $decrypted);