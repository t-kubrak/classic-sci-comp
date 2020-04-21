<?php

require_once "../data_structures.php";

class Nucleotide
{
    const A = 'A';
    const C = 'C';
    const G = 'G';
    const T = 'T';

    const NUCLEOTIDES = [
        self::A => self::A,
        self::C => self::C,
        self::G => self::G,
        self::T => self::T
    ];

    /**
     * @var string
     */
    private string $type;

    private function __construct()
    {
    }

    public static function fromString(string $type): self
    {
        if (!isset(self::NUCLEOTIDES[$type])) {
            throw new InvalidArgumentException();
        }

        $nucleotide = new self();
        $nucleotide->type = $type;

        return $nucleotide;
    }
}

class Codon extends TypedSequence
{
}

class Gene extends TypedSequence
{
}

function string_to_gene(string $geneString): Gene
{
    $gene = Gene::forType(Codon::class);
    $geneChars = str_split($geneString);
    $geneCharsCount = count($geneChars);

    for ($i = 0; $i < $geneCharsCount; $i += 3) {
        if ($i + 2 >= $geneCharsCount) {
            return $gene;
        }

        $codon = Codon::forType(Nucleotide::class)
            ->append(Nucleotide::fromString($geneChars[$i]))
            ->append(Nucleotide::fromString($geneChars[$i + 1]))
            ->append(Nucleotide::fromString($geneChars[$i + 2]));

        $gene->append($codon);
    }

    return $gene;
}

$geneString = "ACGTGGCTCTCTAACGTACGTACGTACGGGGTTTATATATACCCTAGGACTCCCTTT";

$gene = string_to_gene($geneString);

function linear_contains(Gene $gene, Codon $codon): bool
{
    /** @var Codon $geneCodon */
    foreach ($gene as $geneCodon) {
        if ($geneCodon == $codon) {
            return true;
        }
    }

    return false;
}

$codonAcg = Codon::forType(Nucleotide::class)
    ->append(Nucleotide::fromString(Nucleotide::A))
    ->append(Nucleotide::fromString(Nucleotide::C))
    ->append(Nucleotide::fromString(Nucleotide::G));

$codonGat = Codon::forType(Nucleotide::class)
    ->append(Nucleotide::fromString(Nucleotide::G))
    ->append(Nucleotide::fromString(Nucleotide::A))
    ->append(Nucleotide::fromString(Nucleotide::T));

var_dump(linear_contains($gene, $codonAcg));
var_dump(linear_contains($gene, $codonGat));

function binary_contains(Gene $gene, Codon $codon): bool
{
    $low = 0;
    $high = count($gene) - 1;

    while ($low <= $high) {
        $mid = intdiv(($low + $high), 2);

        if ($codon > $gene[$mid]) {
            $low = $mid + 1;
        } elseif ($codon < $gene[$mid]) {
            $high = $mid - 1;
        } else {
            return true;
        }
    }

    return false;
}

$sortedGene = $gene->sort();

var_dump(binary_contains($gene, $codonAcg));
var_dump(binary_contains($gene, $codonGat));