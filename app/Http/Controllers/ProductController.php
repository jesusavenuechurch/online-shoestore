<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // 3 most recent active products for the homepage
        $featured = Product::with(['variants.size', 'variants.color', 'category'])
            ->where('is_active', true)
            ->withCount('variants')
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('featured'));
    }

    public function shop(Request $request)
    {
        $categories = Category::whereNull('parent_id')->get();

        $products = Product::with(['variants.size', 'variants.color', 'category'])
            ->where('is_active', true)
            ->when($request->category, fn ($q) =>
                $q->whereHas('category', fn ($q) =>
                    $q->where('slug', $request->category)
                )
            )
            ->latest()
            ->paginate(12);

        return view('shop', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with([
            'variants.size',
            'variants.color',
            'category',
        ])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        // Group variants by color so the frontend can build the picker
        // Shape: [color_id => ['color' => Color, 'sizes' => [['size' => Size, 'variant' => Variant]]]]
        $variantsByColor = $product->variants
            ->where('is_active', true)
            ->groupBy('color_id')
            ->map(fn ($variants) => [
                'color'   => $variants->first()->color,
                'sizes'   => $variants->map(fn ($v) => [
                    'variant'        => $v,
                    'size'           => $v->size,
                    'in_stock'       => $v->stock_quantity > 0,
                    'low_stock'      => $v->stock_quantity > 0 && $v->stock_quantity <= 3,
                    'effective_price'=> $v->price_override ?? $product->base_price,
                ])->sortBy('size.sort_order')->values(),
            ]);

        // Pass all variants as JSON for Alpine.js reactivity
        $variantsJson = $product->variants
            ->where('is_active', true)
            ->map(fn ($v) => [
                'id'              => $v->id,
                'size_id'         => $v->size_id,
                'color_id'        => $v->color_id,
                'size_label'      => "{$v->size->label} ({$v->size->system})",
                'color_name'      => $v->color->name,
                'hex'             => $v->color->hex_code,
                'stock'           => $v->stock_quantity,
                'effective_price' => (float) ($v->price_override ?? $product->base_price),
                'sku'             => $v->sku,
            ])->values();

        return view('product', compact('product', 'variantsByColor', 'variantsJson'));
    }
}