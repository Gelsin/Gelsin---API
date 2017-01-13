<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 13/01/2017
 * Time: 13:13
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{

    protected $table = 'order_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id', 'delivery_is_now', 'delivery_date', 'address', 'notes'];

}