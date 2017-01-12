<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 12/01/2017
 * Time: 16:16
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{

    use SoftDeletes;

    protected $table = 'branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'market_id', 'address_line', 'latitude', 'longitude'];

}