<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class Setting extends ContentModel
{
    public static function value(string $key, $default = null)
    {
        $request = app()->bound('request') ? request() : null;
        $cacheKey = 'site.settings.values';
        $values = $request?->attributes->get($cacheKey);

        if ($values === null) {
            $values = static::query()
                ->get(['key', 'value', 'is_encrypted'])
                ->mapWithKeys(fn (Setting $setting) => [
                    $setting->key => $setting->is_encrypted
                        ? Crypt::decryptString($setting->value)
                        : $setting->value,
                ])->all();

            $request?->attributes->set($cacheKey, $values);
        }

        return $values[$key] ?? $default;
    }
}
