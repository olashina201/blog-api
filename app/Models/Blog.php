<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'content',
        'user_id'
    ];

    public $append = [
        'image_url'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function comments() {
        return $this->hasMany(Comment::class);
    }
    public function likes() {
        return $this->hasMany(BlogLike::class);
    }

    public function getImageUrlAttribute() {
        return asset('uploads/blog_images/'.$this->image);
    }
}
