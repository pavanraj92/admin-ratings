<?php

namespace admin\ratings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Kyslik\ColumnSortable\Sortable;
use admin\users\Models\User;
use admin\products\Models\Product;

class Rating extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
        'status',
    ];

    protected $sortable = [
        'user_id',
        'product_id',
        'rating',
        'status',
        'created_at',
    ];

    public function scopeFilter($query, $title)
    {
        if (!empty($filters['user'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $filters['user'] . '%']);
            });
        }
    
        if (!empty($filters['product'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['product'] . '%');
            });
        }

        return $query;
    }
    /**
     * filter by status
     */
    public function scopeFilterByStatus($query, $status)
    {
        if (!is_null($status)) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function getStarRatingHtml()
    {
        $starHtml = '';
        for ($i = 0; $i < $this->rating; $i++) {
            $starHtml .= '<img src="' . asset('uploads/ratings/star.png') . '" alt="Image" width="20"> ';
        }
        return $starHtml;
    }

    public static function getPerPageLimit(): int
    {
        return Config::has('get.admin_page_limit')
            ? Config::get('get.admin_page_limit')
            : 10;
    }

    // public function user()
    // {
    //     if (class_exists(\admin\users\Models\User::class)) {
    //         return $this->belongsTo(User::class);
    //     }
    // }

    // public function product()
    // {
    //     if (class_exists(\admin\products\Models\Product::class)) {
    //         return $this->belongsTo(Product::class);
    //     }
    // }
}