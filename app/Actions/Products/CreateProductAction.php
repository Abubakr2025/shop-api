<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Models\Product;

final class CreateProductAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): Product
    {
        // Вся логика создания товара теперь здесь
        return Product::create($data);
    }
}