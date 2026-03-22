<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresencaEquipe extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'data', 'entrada', 'saida'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
