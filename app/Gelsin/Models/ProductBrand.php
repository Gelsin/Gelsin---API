<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 11/01/2017
 * Time: 01:45
 */

namespace App\Gelsin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{

    protected $table = 'pr_brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'cover'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * Get product category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }


}