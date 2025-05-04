<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Olympiad extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ["name", "type", "started_at", "finished_at", "showResult"];


    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
