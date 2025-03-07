<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelPHModel extends Model
{
    use HasFactory;
    protected $table = 'ph';
    protected $fillable = [
        'id_area',
        'ph'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
