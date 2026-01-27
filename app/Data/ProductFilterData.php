<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProductFilterData extends Data
{
    public function __construct(
        public ?string $category = null,
        public ?float $price_min = null,
        public ?float $price_max = null,
        public ?string $search = null,
        public ?string $sort = null,
        public ?int $page = 1,
        public ?int $per_page = 15,
        public ?string $include = null,
        public ?string $fields = null,
    ) {
    }
}
