<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Category;

class PublicController extends Controller
{
    public function home()
    {
        $featuredItem = Item::with(['category', 'status', 'stockmovements' => function ($query) {
            $query->latest();
        }])->latest()->first();

        return view('welcome', [
            'items_count' => Item::count(),
            'stock_movements_count' => StockMovement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'categories_count' => Category::count(),
            'featuredItem' => $featuredItem,
        ]);
    }
}