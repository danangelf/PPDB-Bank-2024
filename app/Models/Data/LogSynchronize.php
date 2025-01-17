<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSynchronize extends Model
{
    use HasFactory;
    
    protected $table = 'log_synchronize';

    protected $keyType = 'string';

    protected $guarded = ['created_at','updated_at'];

    public $incrementing = false;
}
