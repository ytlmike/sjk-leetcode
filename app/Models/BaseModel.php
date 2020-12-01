<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    public static function saveAll($data)
    {
        if (empty($data)) {
            return true;
        }
        $data = array_map(function ($item) {
            $item[static::FIELD_CREATED_AT] = date('Y-m-d H:i:s');
            $item[static::FIELD_UPDATED_AT] = date('Y-m-d H:i:s');
            return $item;
        }, $data);
        return static::insert($data);
    }
}
