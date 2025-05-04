<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasTimestamps;
    use HasTranslations;

    // Указываем, какие поля являются переводимыми
    public array $translatable = [
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'option_f',
        'option_g',
    ];

    protected $fillable = [
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'option_f',
        'option_g',
        'correct_option',
    ];
}
