<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelArusAirModel extends Model
{
    use HasFactory;

    protected $table = 'waterflow';
    protected $fillable = [
        'id_area',
        'debit'
    ];
    protected $hidden = [
        'id',
        'created_at',
    ];
}
