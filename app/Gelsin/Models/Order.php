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
//    protected $fillable = ['customer_id', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->with('customerDetail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($order) {
            $order->detail()->delete();
            $order->products()->delete();
        });
    }

}