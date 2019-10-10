<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const FIELD_ID = 'id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    public function getId()
    {
        return $this->attributes['id'];
    }

    /**
     * @param $id
     * @return static
     */
    public function getById($id)
    {
        return self::where(static::FIELD_ID, $id)->first();
    }

    /**
     * @param $ids
     * @return Collection
     */
    public static function getListById($ids)
    {
        return self::where(self::FIELD_ID, $ids)->get();
    }
}
