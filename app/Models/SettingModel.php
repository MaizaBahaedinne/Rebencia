<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category', 'key_name', 'value', 'type', 'description'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get setting value by key
     */
    public function get($key, $default = null)
    {
        $setting = $this->where('key_name', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['value'], $setting['type']);
    }

    /**
     * Set setting value
     */
    public function setSetting($key, $value)
    {
        $setting = $this->where('key_name', $key)->first();
        
        if ($setting) {
            return $this->update($setting['id'], ['value' => $value]);
        }
        
        return $this->insert([
            'key_name' => $key,
            'value' => $value,
            'category' => 'general'
        ]);
    }

    /**
     * Get all settings by category
     */
    public function getByCategory($category)
    {
        $settings = $this->where('category', $category)->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['key_name']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $result;
    }

    /**
     * Cast value to appropriate type
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
