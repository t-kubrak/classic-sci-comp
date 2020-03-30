<?php

class TypedList implements IteratorAggregate, Countable, ArrayAccess
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

    public function add($value): self
    {
        $this->validate($value);

        $this->values[] = $value;
        return $this;
    }

    /**
     * @param $value
     */
    public function validate($value): void
    {
        if (!$value instanceof $this->type) {
            throw new TypeError("New value is not an instance of type {$this->type}");
        }
    }

    public function isEqualTo(TypedList $otherList): bool
    {
        if ($this->count() != $otherList->count()) {
            return false;
        }

        foreach ($this->values as $key => $value) {
            if ($value != $otherList[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->validate($value);

        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
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

class Codon extends TypedList
{
}

class Gene extends TypedList
{
}

function string_to_gene(string $geneString): Gene
{
    $gene = Gene::forType('Codon');
    $geneChars = str_split($geneString);
    $geneCharsCount = count($geneChars);

    for ($i = 0; $i < $geneCharsCount; $i += 3) {
        if ($i + 2 >= $geneCharsCount) {
            return $gene;
        }

        $codon = Codon::forType('Nucleotide')
            ->add(Nucleotide::fromString($geneChars[$i]))
            ->add(Nucleotide::fromString($geneChars[$i + 1]))
            ->add(Nucleotide::fromString($geneChars[$i + 2]));

        $gene->add($codon);
    }

    return $gene;
}

$geneString = "ACGTGGCTCTCTAACGTACGTACGTACGGGGTTTATATATACCCTAGGACTCCCTTT";

$gene = string_to_gene($geneString);

function linear_contains(Gene $gene, Codon $codon)
{
    /** @var Codon $geneCodon */
    foreach ($gene as $geneCodon) {
        if ($geneCodon == $codon) {
            return true;
        }
    }

    return false;
}

$codonAcg = Codon::forType('Nucleotide')
    ->add(Nucleotide::fromString('A'))
    ->add(Nucleotide::fromString('C'))
    ->add(Nucleotide::fromString('G'));

var_dump(linear_contains($gene, $codonAcg));

$codonGat = Codon::forType('Nucleotide')
    ->add(Nucleotide::fromString('G'))
    ->add(Nucleotide::fromString('A'))
    ->add(Nucleotide::fromString('T'));

var_dump(linear_contains($gene, $codonGat));