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

class Category extends Model
{
    use SoftDeletes;


    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'parent_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at',
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get category products.
     *
     * @return string
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }



}