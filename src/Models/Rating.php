<?php

namespace admin\ratings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Schema;

class Rating extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'course_id',
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

    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];

            if (Schema::hasTable('users') && method_exists($this, 'user')) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                });
            }

            if (Schema::hasTable('products') && method_exists($this, 'product')) {
                $query->orWhereHas('product', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }

            if (Schema::hasTable('courses') && method_exists($this, 'course')) {
                $query->orWhereHas('course', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            }

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

    public function user()
    {
        if (class_exists(\admin\users\Models\User::class)) {
            return $this->belongsTo(\admin\users\Models\User::class, 'user_id');
        }
    }
    public function product()
    {
        if (class_exists(\admin\products\Models\Product::class)) {
            return $this->belongsTo(\admin\products\Models\Product::class);
        }
    }

    public function course()
    {
        if (class_exists(\admin\courses\Models\Course::class)) {
            return $this->belongsTo(\admin\courses\Models\Course::class);
        }
    }
}