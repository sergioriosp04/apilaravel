<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //Indicar la tabla a la que hace referencia este modelo
    protected $table = 'categories';

    //indicar relaciones
    // relacion one to many}
    public function posts(){
        return $this->hasMany('App\Post'); // sacar todos los posts que esten relacionados con cierta categoria
                               //El modelo al cual se relaciona
    }
}
