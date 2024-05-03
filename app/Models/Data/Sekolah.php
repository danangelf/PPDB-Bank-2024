<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    
    protected $table = 'sekolah';

    protected $keyType = 'string';

    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at'];

    protected $guarded = ['created_at','updated_at'];

    public $incrementing = false;
}
