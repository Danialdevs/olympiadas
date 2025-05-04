<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Participant extends Model
{
    protected static function booted()
    {
        static::creating(function ($participant) {
            if (empty($participant->code)) {
                do {
                    $code = 'BOLASAQ-2025-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                } while (self::where('code', $code)->exists());

                $participant->code = $code;
            }
        });
    }

    protected $fillable = [
        'full_name',
        'school',
        'code',
        'total_score',
        'finished_time',
        'used',
        'language',
        'olympiad_id',
    ];

    public function olympiad()
    {
        return $this->belongsTo(Olympiad::class);
    }
}
