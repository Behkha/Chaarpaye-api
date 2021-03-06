<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Celebrity extends Model
{
    const ContactTypes = [
        'tell', 'email'
    ];
    const SINGLE_CACHE_TIME = 30;
    protected $guarded = [];
    protected $casts = [
        'media' => 'array',
        'contact' => 'array'
    ];

    protected $appends = [
        'jobs'
    ];


    public static function getById($id)
    {
        return self::with(['character.events', 'character.types'])->findOrFail($id);
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::deleted(function ($celeb) {
            $celeb->character()->delete();
            $celeb->comments()->delete();
            $celeb->clearPropertiesCache();
        });

        self::updating(function ($celeb) {
            $celeb->clearCache();
            $celeb->clearPropertiesCache();
        });
    }

    public function clearCache()
    {
    }

    public function clearPropertiesCache()
    {
        foreach ($this->character->events as $event) {
            Event::forgetCache($event->id);
        }
    }


    public function character()
    {
        return $this->morphOne('App\Models\Character', 'character');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function getJobsAttribute()
    {

        $jobs = '';
        if (!$this->character) {
            return $jobs;
        }
        foreach ($this->character->types as $type) {
            $jobs = $jobs . ', ' . $type->title;
        }
        $jobs = mb_substr($jobs, 1);

        return $jobs;
    }
}
