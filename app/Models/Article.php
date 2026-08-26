<?php
namespace App\Models;
class Article extends ContentModel { protected $casts=['published_at'=>'datetime','translations'=>'array']; public function category(){return $this->belongsTo(Category::class);} }
