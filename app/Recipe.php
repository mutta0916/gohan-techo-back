<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Recipe extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function howtos ()
    {
        return $this->hasMany('App\RecipeHowto');
    }

    public function ingredients ()
    {
        return $this->hasMany('App\RecipeIngredient');
    }
}
