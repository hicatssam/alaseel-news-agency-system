<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function set(
        string $key,
        mixed $value,
        ?string $type = null,
        ?string $group = null
    ): void {
        $setting = static::query()->firstOrNew(['key' => $key]);
        $setting->value = $value;

        if ($type !== null) {
            $setting->type = $type;
        } elseif (! $setting->exists && blank($setting->type)) {
            $setting->type = 'text';
        }

        if ($group !== null) {
            $setting->group = $group;
        }

        $setting->save();
    }
}