<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExcelQueue extends Model
{
    use HasFactory;

    protected $table = 'user_excel_queue';

    protected $fillable = [
        'user_id',
        'type',
        'original_name',
        'file_path',
        'parameters',
        'status',
        'total_rows',
        'success_rows',
        'failed_rows',
        'row_errors',
    ];

    protected $casts = [
        'parameters' => 'array',
        'row_errors' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}