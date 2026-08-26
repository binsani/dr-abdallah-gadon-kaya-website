<?php
namespace App\Models;
class ActivityLog extends ContentModel { protected $casts=['properties'=>'array']; public function user(){return $this->belongsTo(User::class);} }
