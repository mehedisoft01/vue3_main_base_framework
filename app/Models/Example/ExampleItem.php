<?php

namespace App\Models\Example;

use Illuminate\Database\Eloquent\Model;

class ExampleItem extends Model
{
    protected $table = 'example_items';
    protected $fillable = ['example_id','item_name','quantity','rate','amount','remarks'];
}
