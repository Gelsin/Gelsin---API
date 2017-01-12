<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 12/01/2017
 * Time: 02:28
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{

    protected $table = 'pr_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'product_id', 'is_covered'];


}