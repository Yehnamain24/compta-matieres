<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'movement_type_id',
        'user_id',
        'quantity',
        'stock_initial',
        'stock_final',
        'movement_date',
        'note',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function movementType()
    {
        return $this->belongsTo(MovementType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcule le stock final selon le type de mouvement.
     */
    public function calculerStockFinal(): int
    {
        $type = strtolower(str_replace(['é','è','ê'], 'e', $this->movementType->name ?? ''));

        if ($type === 'sortie') {
            return max(0, $this->stock_initial - $this->quantity);
        }

        // Entrée et Retour : on ajoute
        return $this->stock_initial + $this->quantity;
    }
}


   