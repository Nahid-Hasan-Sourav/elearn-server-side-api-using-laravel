<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function courseCategory(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function courseSubCategory(){
        return $this->belongsTo(courseSubCategory::class,'sub_category_id','id');
    }
}
