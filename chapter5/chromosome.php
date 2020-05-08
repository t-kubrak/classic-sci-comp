<?php

interface Chromosome
{
    public function fitness(): float;

    public static function randomInstance(): self;

    public function crossover(Chromosome $chromosome): array;

    public function mutate(): void;
}