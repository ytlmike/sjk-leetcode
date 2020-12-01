<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Models
 * @property Collection|UserSubmit[] $submits
 * @property Collection|UserPoint[] $points
 */
class User  extends Authenticatable implements JWTSubject
{
    const FIELD_NAME = 'name';
    const FIELD_PASSWORD = 'password';
    const FIELD_SLUG = 'leetcode_slug';
    const FIELD_AVATAR = 'avatar';
    const FIELD_STATUS = 'status';

    const STATUS_NORMAL = 1;
    const STATUS_BLACK = 0;

    const CHANGEABLE_FIELDS = [
        self::FIELD_NAME,
        self::FIELD_PASSWORD
    ];

    protected $table = 'sjk_user';

    public function getName()
    {
        return $this->attributes[self::FIELD_NAME];
    }

    public function setName($name)
    {
        $this->attributes[self::FIELD_NAME] = $name;
        return $this;
    }

    public function getAvatar()
    {
        return $this->attributes[self::FIELD_AVATAR];
    }

    public function setAvatar($avatar)
    {
        $this->attributes[self::FIELD_AVATAR] = $avatar;
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

    public function getStatus()
    {
        return $this->attributes[self::FIELD_STATUS];
    }

    public function setStatus($status)
    {
        $this->attributes[self::FIELD_STATUS] = $status;
        return $this;
    }

    public function getPassword()
    {
        return $this->attributes[self::FIELD_PASSWORD];
    }

    public function setPassword($password)
    {
        $this->attributes[self::FIELD_PASSWORD] = bcrypt($password);
        return $this;
    }

    public function submits()
    {
        return $this->hasMany(UserSubmit::class, UserSubmit::FIELD_USER_ID);
    }

    public function points()
    {
        return $this->hasMany(UserPoint::class, UserPoint::FIELD_USER_ID);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
