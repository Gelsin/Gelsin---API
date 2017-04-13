<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 01/02/2017
 * Time: 01:31
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $table = 'couriers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'address', 'contact'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'courier_id');
    }




}