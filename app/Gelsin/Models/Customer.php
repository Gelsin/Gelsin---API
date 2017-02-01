<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 01/02/2017
 * Time: 01:31
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'points', 'contact'];

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

}