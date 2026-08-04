<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message', 'status', 'read_at', 'replied_at'])]
class ContactMessage extends Model
{
    public const StatusNew = 'new';

    public const StatusRead = 'read';

    public const StatusReplied = 'replied';

    protected $attributes = [
        'status' => self::StatusNew,
    ];

    public function markAsRead(): void
    {
        $this->forceFill([
            'status' => self::StatusRead,
            'read_at' => $this->read_at ?? now(),
        ])->save();
    }

    public function markAsReplied(): void
    {
        $this->forceFill([
            'status' => self::StatusReplied,
            'read_at' => $this->read_at ?? now(),
            'replied_at' => now(),
        ])->save();
    }

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }
}
