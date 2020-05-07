<?php

interface Chromosome
{
    public function fitness(): float;

    public function randomInstance(): self;

    public function crossover(): array;

    public function mutate(): void;
}