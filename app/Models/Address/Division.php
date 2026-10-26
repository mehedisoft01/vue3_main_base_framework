<?php

namespace App\Models\Address;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{

    protected $table = 'divisions';
    protected $fillable = ['division_name','division_code','status'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'division_name' => 'required',
        ]);

        return $validate;
    }

}
