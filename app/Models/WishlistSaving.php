<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistSaving extends Model
{
    use HasFactory;

    protected $fillable = ['wishlist_id', 'amount', 'date', 'note'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    protected static function booted(): void
    {
        // Setiap nambah tabungan baru -> otomatis nambah collected_amount di Wishlist
        static::created(function (WishlistSaving $saving) {
            $saving->wishlist->increment('collected_amount', $saving->amount);
            $saving->wishlist->refreshStatus();
        });

        // Kalau nominal diedit -> sesuaikan selisihnya
        static::updated(function (WishlistSaving $saving) {
            if ($saving->wasChanged('amount')) {
                $diff = $saving->amount - $saving->getOriginal('amount');
                $saving->wishlist->increment('collected_amount', $diff);
                $saving->wishlist->refreshStatus();
            }
        });

        // Kalau riwayat dihapus -> kurangin lagi collected_amount-nya
        static::deleted(function (WishlistSaving $saving) {
            $saving->wishlist->decrement('collected_amount', $saving->amount);
            $saving->wishlist->refreshStatus();
        });
    }
}
