<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respon extends Model
{
    protected $table = 'responses';
    protected $fillable = ['survey_id', 'user_id', 'jawaban', 'dikirim_at'];
    protected $casts = ['jawaban' => 'array', 'dikirim_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function survey() { return $this->belongsTo(Survey::class); }
}
