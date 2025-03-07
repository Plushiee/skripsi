<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelTempHumModel extends Model
{
    use HasFactory;
    protected $table = 'temphum';
    protected $fillable = [
        'id_area',
        'temperature',
        'humidity'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
