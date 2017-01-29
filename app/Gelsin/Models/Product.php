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

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $cover_url;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'branch_id', 'quantity', 'price', 'is_discounted',];

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
        'deleted_at',
        'updated_at',
        'created_at',
        'sub_category_id',
    ];

    /**
     * Get product category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    protected $appends = ['cover_url'];

    /**
     * Get full url of cover image
     *
     * @return string
     */
    public function getCoverUrlAttribute()
    {

        return url() . "/api/product/image/" . $this->id;
    }


}