<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public function chirp()
    {
        return $this->belongsTo(Chirp::class);
    }

    protected $fillable = [
        'filename',
        'thumbnail'
    ];
}
