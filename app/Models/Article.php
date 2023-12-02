<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    // If your database name is not plural, you must define with line below
    // protected $table = 'articles';

    protected $fillable = [
        'user_id',
        'sub_category_id',
        'title',
        'content',
        'images',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer', 
        'sub_category_id' => 'integer',
    ];

    public function getImagesAttribute($value)
    {
        if ($value != null) {
            $attachments = json_decode($value);
            $attachmentsUrl = array_map(function ($e) {
                return StorageHelper::getFileUrl($e);
            }, $attachments);
            return $attachmentsUrl;
        }
    }
}
