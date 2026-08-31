<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name', 'target_price', 'collected_amount', 'image', 'status', 'notes',
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
        'collected_amount' => 'decimal:2',
    ];

    public function savings()
    {
        return $this->hasMany(WishlistSaving::class);
    }

    public function getProgressPercentAttribute()
    {
        if ($this->target_price <= 0) return 0;
        $percent = ($this->collected_amount / $this->target_price) * 100;
        return $percent > 100 ? 100 : round($percent, 1);
    }

    public function getRemainingAttribute()
    {
        $remaining = $this->target_price - $this->collected_amount;
        return $remaining > 0 ? $remaining : 0;
    }

    public function refreshStatus(): void
    {
        $this->refresh();
        $status = $this->collected_amount >= $this->target_price ? 'tercapai' : 'proses';
        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
