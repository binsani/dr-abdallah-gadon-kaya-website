<?php
namespace App\Models;
use Illuminate\Support\Facades\Crypt;
class Setting extends ContentModel { public static function value(string $key,$default=null){$s=static::where('key',$key)->first(); if(!$s)return $default; return $s->is_encrypted?Crypt::decryptString($s->value):$s->value;} }
