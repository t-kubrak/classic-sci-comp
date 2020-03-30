<?php

class TypedList implements IteratorAggregate
{
    protected string $type;
    protected array $values = [];

    public static function forType(string $type): self
    {
        $list = new static();
        $list->type = $type;
        return $list;
    }

    protected function __construct()
    {
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function add($newval): self
    {
        if (!$newval instanceof $this->type) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        }

        $this->values[] = $newval;
        return $this;
    }
}

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

class Codon extends TypedList {}

class Gene extends TypedList {}

function string_to_gene(string $geneString): Gene {
    $gene = Gene::forType('Codon');
    $geneChars = str_split($geneString);
    $geneCharsCount = count($geneChars);

    for ($i = 0; $i < $geneCharsCount; $i+= 3) {
        if ($i + 2 >= $geneCharsCount) {
            return $gene;
        }

        $codon = Codon::forType('Nucleotide');
        $codon->add(Nucleotide::fromString($geneChars[$i]))
            ->add(Nucleotide::fromString($geneChars[$i+1]))
            ->add(Nucleotide::fromString($geneChars[$i+2]));

        $gene->add($codon);
    }

    return $gene;
}

$geneString = "ACGTGGCTCTCTAACGTACGTACGTACGGGGTTTATATATACCCTAGGACTCCCTTT";

$gene = string_to_gene($geneString);

