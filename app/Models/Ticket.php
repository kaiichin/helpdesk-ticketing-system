<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'Open';
    public const STATUS_ON_PROGRESS = 'On Progress';
    public const STATUS_RESOLVED = 'Resolved';
    public const STATUS_CLOSED = 'Closed';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_ON_PROGRESS,
        self::STATUS_RESOLVED,
        self::STATUS_CLOSED,
    ];

    public const NEXT_STATUSES = [
        self::STATUS_OPEN => self::STATUS_ON_PROGRESS,
        self::STATUS_ON_PROGRESS => self::STATUS_RESOLVED,
        self::STATUS_RESOLVED => self::STATUS_CLOSED,
    ];

    protected $fillable = [
        'ticket_no',
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
    ];

    public static function generateTicketNo(): string
    {
        $datePart = now()->format('Ymd');

        $latestTicket = self::where('ticket_no', 'like', 'TCK-' . $datePart . '-%')
            ->orderByDesc('ticket_no')
            ->first();

        $nextNumber = 1;

        if ($latestTicket) {
            $nextNumber = ((int) substr($latestTicket->ticket_no, -4)) + 1;
        }

        return 'TCK-' . $datePart . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function nextStatus(): ?string
    {
        return self::NEXT_STATUSES[$this->status] ?? null;
    }

    public function canMoveTo(string $newStatus): bool
    {
        return $this->nextStatus() === $newStatus;
    }
}
