<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationCode extends Model
{
    protected $fillable = ['code', 'created_by', 'used_by', 'used_at', 'expires_at'];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function usedBy() { return $this->belongsTo(User::class, 'used_by'); }
}
