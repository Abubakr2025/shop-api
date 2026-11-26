<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_order(): void
    {
        // 1. Отправляем запрос без токена
        $this->postJson('/api/v1/orders', [
            'items' => [['product_id' => 1, 'qty' => 1]]
        ])->assertUnauthorized(); // Ожидаем 401
    }

    public function test_user_can_create_order_if_product_in_stock(): void
    {
        // 1. Создаем пользователя и товар (через фабрику)
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price_minor' => 1000]);

        // 2. Авторизуем пользователя
        Sanctum::actingAs($user);

        // 3. Отправляем запрос на создание заказа
        $response = $this->postJson('/api/v1/orders', [
            'items' => [['product_id' => $product->id, 'qty' => 2]]
        ]);

        // 4. Проверяем, что заказ создан (201)
        $response->assertCreated();

        // 5. Проверяем, что остаток на складе уменьшился (10 - 2 = 8)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_order_fails_if_not_enough_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);
        Sanctum::actingAs($user);

        // Пытаемся купить 5 штук, когда есть только 1
        $response = $this->postJson('/api/v1/orders', [
            'items' => [['product_id' => $product->id, 'qty' => 5]]
        ]);

        $response->assertStatus(409); // 409 Conflict — это то, что мы настроили для DomainException
        
        // Проверяем, что в сообщении есть слово "Недостаточно"
        $this->assertStringContainsString('Недостаточно', $response->json('message'));
    }
}