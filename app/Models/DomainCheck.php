<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $domain_id
 * @property bool $is_up
 * @property int|null $status_code
 * @property int|null $response_time_ms
 * @property string|null $error
 * @property Carbon $checked_at
 */
class DomainCheck extends Model
{
    /** @use HasFactory<\Database\Factories\DomainCheckFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['domain_id', 'is_up', 'status_code', 'response_time_ms', 'error', 'checked_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_up' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Domain, $this> */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /** @return HasMany<DomainCheckNotification, $this> */
    public function notifications(): HasMany
    {
        return $this->hasMany(DomainCheckNotification::class);
    }
}
