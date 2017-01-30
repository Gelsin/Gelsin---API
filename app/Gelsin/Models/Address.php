<?php
/**
 * Created by PhpStorm.
 * User: alirzayev
 * Date: 31/01/2017
 * Time: 01:59
 */

namespace App\Gelsin\Models;


use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'address_line', 'branch_address_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branchAddress()
    {
        return $this->belongsTo(BranchAdress::class, "branch_address_id");
    }


}