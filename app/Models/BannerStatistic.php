<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerStatistic extends Model
{
    public $fillable = [
        'all',
        'unique',
        'date'
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
