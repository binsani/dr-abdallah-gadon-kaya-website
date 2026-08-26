<?php
namespace App\Models;
class Video extends ContentModel { protected $casts=['published_at'=>'datetime','is_featured'=>'boolean','is_hidden'=>'boolean']; }
