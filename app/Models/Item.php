<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'quantity',
        'alert_threshold',
        'category_id',
        'status_id',
        'user_id'
    ];

    // Relations originales (anglais)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockmovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // --- Alias en français ---

    public function categorie(): BelongsTo
    {
        return $this->category();
    }

    public function statut(): BelongsTo
    {
        return $this->status();
    }

    public function getNomAttribute()
    {
        return $this->name;
    }

    public function getQuantiteAttribute()
    {
        return $this->quantity;
    }

    public function getEmplacementAttribute()
    {
        return $this->location;
    }
}