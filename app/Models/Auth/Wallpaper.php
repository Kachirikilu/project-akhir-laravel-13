<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Wallpaper extends Model
{
    protected $fillable = [
        'user_id',
        'path',
        'is_custom',
        'is_active',
        'opacity',
        'brightness'
    ];

    protected $table = 'user_wallpapers';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}