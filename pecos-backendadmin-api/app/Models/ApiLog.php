<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    protected $table = 'api_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'dropshipper_id',
        'endpoint',
        'method',
        'request_body',
        'response_code',
        'response_body',
        'ip_address',
        'user_agent',
        'duration_ms',
        'country',
    ];

    protected $casts = [
        'response_code' => 'integer',
        'duration_ms' => 'integer',
        'request_body' => 'array',
        'response_body' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the dropshipper for this log.
     */
    public function dropshipper(): BelongsTo
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id');
    }

    /**
     * Scope by HTTP method.
     */
    public function scopeMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * Scope by response code.
     */
    public function scopeResponseCode($query, $code)
    {
        return $query->where('response_code', $code);
    }

    /**
     * Scope for successful requests.
     */
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('response_code', [200, 299]);
    }

    /**
     * Scope for failed requests.
     */
    public function scopeFailed($query)
    {
        return $query->where('response_code', '>=', 400);
    }
}
