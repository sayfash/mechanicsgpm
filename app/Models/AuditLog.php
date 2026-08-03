<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null; // Audit logs shouldn't be updated

    protected $fillable = [
        'user_id',
        'action_type',
        'target_table',
        'record_id',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
