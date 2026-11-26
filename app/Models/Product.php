<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Белый список полей для массового заполнения
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_minor',
        'stock',
        'is_active',
    ];

    // Приведение типов при чтении из БД
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price_minor' => 'integer',
            'stock' => 'integer',
        ];
    }
}
