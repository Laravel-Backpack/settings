<?php

namespace Backpack\Settings\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Config;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;

    protected $fillable = ['value'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('backpack.settings.table_name', 'settings');
    }

    /**
     * Grab a setting value from the database.
     *
     * @param string $key The setting key, as defined in the key db column
     *
     * @return string The setting value.
     */
    public static function get($key)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->first();

        if (!$entry) {
            return;
        }

        return $entry->value;
    }

    /**
     * Update a setting's value.
     *
     * @param string $key   The setting key, as defined in the key db column
     * @param string $value The new value.
     */
    public static function set($key, $value = null)
    {
        $prefixed_key = config('backpack.settings.table_name', 'settings').'.'.$key;
        $setting = new self();
        $entry = $setting->where('key', $key)->firstOrFail();

        // update the value in the database
        $entry->value = $value;
        $entry->saveOrFail();

        // update the value in the session
        Config::set($prefixed_key, $value);

        if (Config::get($prefixed_key) == $value) {
            return true;
        }

        return false;
    }
}
