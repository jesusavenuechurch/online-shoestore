<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    private string $key = 'cart';

    public function get(): Collection
    {
        return collect(session($this->key, []));
    }

    public function add(int $variantId, int $quantity = 1): void
    {
        $cart = session($this->key, []);
        $variant = ProductVariant::with(['product', 'size', 'color'])->findOrFail($variantId);

        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = [
                'variant_id'   => $variant->id,
                'product_name' => $variant->product->name,
                'brand'        => $variant->product->brand,
                'size'         => $variant->size->label . ' (' . $variant->size->system . ')',
                'color'        => $variant->color->name,
                'hex'          => $variant->color->hex_code,
                'sku'          => $variant->sku,
                'unit_price'   => (float) ($variant->price_override ?? $variant->product->base_price),
                'quantity'     => $quantity,
                'images'       => $variant->product->images ?? [],
            ];
        }

        session([$this->key => $cart]);
    }

    public function remove(int $variantId): void
    {
        $cart = session($this->key, []);
        unset($cart[$variantId]);
        session([$this->key => $cart]);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = session($this->key, []);
        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId]['quantity'] = $quantity;
        }
        session([$this->key => $cart]);
    }

    public function clear(): void
    {
        session()->forget($this->key);
    }

    public function subtotal(): float
    {
        return $this->get()->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
    }

    public function count(): int
    {
        return $this->get()->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->get()->isEmpty();
    }
}