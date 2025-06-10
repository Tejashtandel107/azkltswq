<?php

namespace App\Models;

use Auth;
use Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Storage;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable,SoftDeletes;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'role_id', 'firstname', 'lastname', 'username', 'email', 'password', 'phone', 'photo', 'isactive',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/

    /**
     * Get the user's full name.
     *
     * @param  string  $value
     * @return string
     */
    public function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => "{$this->firstname} {$this->lastname}",
        );
    }

    /**
     * Get the user's photo.
     *
     * @param  string  $value
     * @return string
     */
    public function photo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::getProfileImg($value),
        );
    }

    public function logo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::getProfileImg($value),
        );
    }

    /**
     * Delete File from storage
     */
    public function deleteFile($file = '')
    {
        if (Storage::exists($file)) {
            return Storage::delete($file);
        }

        // Optional: return false or throw exception if file not found
        return false;
    }

    public function getRoleId($user_id = 0)
    {
        if ($user_id > 0) {
            $user = $this->find($user_id);
        } elseif (Auth::check()) {
            $user = Auth::user();
        }
        if (isset($user)) {
            $role_id = $user->role_id;

            return $role_id;
        }

        return null;
    }
}
