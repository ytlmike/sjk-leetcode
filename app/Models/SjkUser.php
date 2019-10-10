<?php


namespace App\Models;

class SjkUser extends BaseModel
{
    const TABLE_NAME = 'sjk_user';

    const FIELD_NAME = 'name';
    const FIELD_SLUG = 'leetcode_slug';

    protected $table = self::TABLE_NAME;
    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_SLUG
    ];

    public function getName()
    {
        return $this->attributes[self::FIELD_NAME];
    }

    public function setName($name)
    {
        $this->attributes[self::FIELD_NAME] = $name;
        return $this;
    }

    public function getSlug()
    {
        return $this->attributes[self::FIELD_SLUG];
    }

    public function setSlug($slug)
    {
        $this->attributes[self::FIELD_SLUG] = $slug;
        return $this;
    }

    public function submits()
    {
        return $this->hasMany(UserSubmit::class, UserSubmit::FIELD_USER_ID);
    }
}
