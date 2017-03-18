<?php
/**
 * This Model is General App Setting Model
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 16/03/2017
 * Time: 00:03
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value'];

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}