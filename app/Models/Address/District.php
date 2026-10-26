<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class District extends Model
{
    protected $table = 'districts';
    protected $fillable = ['district_name','division_id','status'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'district_name' => 'required',
            'division_id' => '',

        ]);

        return $validate;
    }

    public function division ()
    {
        return $this->belongsTo(Division::class,'division_id','id');
    }
}
