<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaModel extends Model
{
    use HasFactory;
    protected $table = 'area';
    protected $fillable = [
        'nama-area',
        'lokasi',
        'keterangan',
    ];
    protected $hidden = [
        'id_area',
        'created_at',
    ];
}
