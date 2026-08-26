<?php
namespace App\Models;
class Event extends ContentModel { protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime','is_recurring'=>'boolean']; }
