<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $domain_check_id
 * @property string $channel
 * @property string $status
 * @property int $attempts
 * @property string|null $last_error
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DomainCheckNotification extends Model
{
    /** @var list<string> */
    protected $fillable = ['domain_check_id', 'channel', 'status', 'attempts', 'last_error', 'sent_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<DomainCheck, $this> */
    public function check(): BelongsTo
    {
        return $this->belongsTo(DomainCheck::class, 'domain_check_id');
    }
}
