<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelPompaModel extends Model
{
    use HasFactory;

    protected $table = 'pompa';
    protected $fillable = [
        'id_area',
        'status',
        'otomatis',
        'suhu'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
