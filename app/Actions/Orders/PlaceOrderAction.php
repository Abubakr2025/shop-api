<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

final class PlaceOrderAction
{
    public function execute(PlaceOrderData $data): Order
    {
        return DB::transaction(function () use ($data): Order {

            $order = Order::create([
                'user_id' => $data->userId,
                'status' => 'pending',
                'total_minor' => 0,
            ]);

            $total = 0;

            foreach ($data->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['qty']) {
                    throw new \App\Exceptions\DomainException("Недостаточно товара на складе");
                }

                $product->decrement('stock', $item['qty']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price_minor' => $product->price_minor,
                ]);

                $total += $product->price_minor * $item['qty'];
            }

            $order->update(['total_minor' => $total]);

            return $order;
        });
    }
}
