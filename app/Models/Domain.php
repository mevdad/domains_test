<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $check_interval
 * @property int $check_timeout
 * @property string $check_method
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DomainCheck|null $latestCheck
 */
class Domain extends Model
{
    /** @use HasFactory<\Database\Factories\DomainFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['user_id', 'name', 'check_interval', 'check_timeout', 'check_method'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<DomainCheck, $this> */
    public function checks(): HasMany
    {
        return $this->hasMany(DomainCheck::class)->orderByDesc('checked_at');
    }

    /** @return HasOne<DomainCheck, $this> */
    public function latestCheck(): HasOne
    {
        return $this->hasOne(DomainCheck::class)->latestOfMany('checked_at');
    }
}
