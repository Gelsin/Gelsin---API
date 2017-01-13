<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 13/01/2017
 * Time: 13:12
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_id', 'status'];

}