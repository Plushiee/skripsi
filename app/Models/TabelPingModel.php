<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelPingModel extends Model
{
    use HasFactory;

    protected $table = 'ping';
    protected $fillable = [
        'id_area',
        'ping'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
