<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ===== STATS =====
        $totalItems = Item::where('user_id', $user->id)->count();
        $totalCategories = Category::where('user_id', $user->id)->count();

        // Mouvements du mois en cours
        $movementsThisMonth = StockMovement::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Matériels sous seuil d'alerte
        $itemsUnderThreshold = Item::where('user_id', $user->id)
            ->whereColumn('quantity', '<=', 'alert_threshold')
            ->count();

        // ===== GRAPHIQUE : Quantité totale par catégorie =====
        $dataParCategorie = Category::where('user_id', $user->id)
            ->withSum('items', 'quantity')
            ->get();

        // Structure attendue par Chart.js : [{ label: 'Nom', total: 50 }, ...]
        $categoriesChartData = $dataParCategorie->map(function ($cat) {
            return [
                'label' => $cat->name,
                'total' => (int) ($cat->items_sum_quantity ?? 0),
            ];
        })->values();

        // ===== DERNIERS MOUVEMENTS =====
        $derniersMouvements = StockMovement::with(['item', 'movementType'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // ===== MATÉRIELS SOUS SEUIL D'ALERTE (liste) =====
        $lowStockItems = Item::where('user_id', $user->id)
            ->whereColumn('quantity', '<=', 'alert_threshold')
            ->with('category')
            ->get();

        return view('User.dashboard', compact(
            'totalItems',
            'movementsThisMonth',
            'totalCategories',
            'itemsUnderThreshold',
            'categoriesChartData',
            'derniersMouvements',
            'lowStockItems'
        ));
    }
}