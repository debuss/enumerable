<?php

namespace Tests\Entity;

class Person
{

    public function __construct(
        public string $name,
        public int $age
    ) {}
}
