<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Upazila extends Model
{
    protected $table = 'upazilas';
    protected $fillable = ['upazila_name','district_id','division_id','upazila_code','status'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'upazila_name' => 'required',
            'district_id' => '',
            'division_id' => '',

        ]);

        return $validate;
    }

    public function district ()
    {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function division ()
    {
        return $this->belongsTo(Division::class,'division_id','id');
    }
}
