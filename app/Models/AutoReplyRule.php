<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoReplyRule extends Model
{
    use HasFactory;
    protected $fillable =[
        'keywords',
        'response',
        'is_active',
        'is_fallback'
    ];
    protected $casts=[
        'keywords' =>'array',
      'is_active' =>'boolean',

    ];
}
