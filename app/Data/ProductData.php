<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public string $category = '',
        public float $price = 0,
        public ?float $discount_percentage = null,
        public ?float $rating = null,
        public int $stock = 0,
    ) {
    }
}
