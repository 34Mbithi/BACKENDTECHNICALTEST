<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $category = null,
        public ?float $price = null,
        public ?float $discount_percentage = null,
        public ?float $rating = null,
        public ?int $stock = null,
    ) {
    }
}
