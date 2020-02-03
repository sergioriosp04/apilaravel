<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Indicar la tabla a la que hace referencia este modelo
    protected $table = 'posts';

    //relacion many to one
    public function user(){
        return $this->belongsTo('App\User', 'user_id'); //un usuario puede tener muchos posts
                          // el modelo al que hace referencia, la llave foranea del modelo de Post
    }
    //relacion many to one
    public function Category(){
        return $this->belongsTo('App\Category', 'category_id'); //una categoria puede tener muchos posts
                   // el modelo al que hace referencia, la llave foranea del modelo de Post
    }
}
