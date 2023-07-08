<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory;

    // Kalo mau pake deleted_at maka gunain SoftDeletes
    // use SoftDeletes;

    // If your database name is not plural, you must define with line below
    // protected $table = 'blogs';

    
    public $timestamps = true;

    // Untuk define field apa aja yang bisa diisi
    // Ex : title tidak ada, maka saat POST title, body --> yang akan masuk DB cuma body aja
    protected $fillable = [
        'title',
        'body',
    ];

    // public $sortable = ['name',
    // 'sku',
    // 'category_id',
    // 'price'];
}
