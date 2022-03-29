<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Comment extends Model
{
    use SoftDeletes;
    const STATUSES = [
        'WAITING' => '1',
        'CONFIRMED' => '2',
        'REJECTED' => '3'
    ];
    const CACHE_PERIOD = 20;
    protected $appends = ['created_at_fa_h'];
    protected $guarded = [];

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::created(function ($comment) {

        });

        self::updated(function ($comment) {
            $type = ucfirst($comment->commentable_type);
        });

        self::deleted(function ($comment) {
            $type = ucfirst($comment->commentable_type);
        });

    }

    public function scopePending($query)
    {
        $query->where('status_id', self::STATUSES['WAITING']);
    }

    public function detail()
    {
        return $this->hasOne('App\Models\CommentDetail', 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function scopeConfirmed($query)
    {
        return $query->whereStatusId(self::STATUSES['CONFIRMED']);
    }

    public function getCreatedAtFaHAttribute()
    {
        $year_diff = $this->created_at->diffInYears(Carbon::now());
        $month_diff = $this->created_at->diffInMonths(Carbon::now());
        $day_diff = $this->created_at->diffInDays(Carbon::now());
        $hour_diff = $this->created_at->diffInHours(Carbon::now());
        $min_diff = $this->created_at->diffInMinutes(Carbon::now());
        $sec_diff = $this->created_at->diffInSeconds(Carbon::now());

        if ($year_diff !== 0) {
            return $year_diff . ' سال پیش';
        }

        if ($month_diff !== 0) {
            return $month_diff . ' ماه پیش';
        }

        if ($day_diff !== 0) {
            return $day_diff . ' روز پیش';
        }

        if ($hour_diff !== 0) {
            return $hour_diff . ' ساعت پیش';
        }

        if ($min_diff !== 0) {
            return $min_diff . ' دقیقه پیش';
        }

        return $sec_diff . ' ثانیه پیش';
    }

}