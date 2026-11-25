<?php

namespace App\Models\Example;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Example extends Model
{
    protected $table = 'examples';
    protected $fillable = ['user_id','example_name','example_date','month','remarks','total_qty','grand_total','status'];

    public function validate($input = []){

        $validate = Validator::make($input,[
            'example_name' => 'required',
            'example_date' => 'required'
        ]);

        return $validate;
    }

    public function exampleItems()
    {
        return $this->hasMany(ExampleItem::class, 'example_id', 'id');
    }

    // =====================================================
    // DELETE RELATED ITEMS WHEN EXAMPLE IS DELETED
    // =====================================================

    protected static function booted()
    {
        static::deleting(function ($example) {
            $example->exampleItems()->delete();
        });
    }
}
