<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelTDSModel extends Model
{
    use HasFactory;

    protected $table = 'tds';
    protected $fillable = [
        'id_area',
        'ppm'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
