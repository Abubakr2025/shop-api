<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Actions\Orders\PlaceOrderData;
use App\Actions\Orders\PlaceOrderAction;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderController extends Controller
{
    public function store(Request $request, PlaceOrderAction $action): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $order = $action->execute(new PlaceOrderData(
                userId: $request->user()->id,
                items: $validated['items'],
            ));

            return response()->json([
                'message' => 'Заказ успешно создан',
                'order_id' => $order->id,
                'total' => $order->total_minor / 100,
            ], 201);

        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return OrderResource::collection($orders);
    }
}
