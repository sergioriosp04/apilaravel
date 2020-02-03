<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    //
    public function testOrm(){

        $posts = Post::all();
        foreach ($posts as $post){
            echo $post->title. ':   '. $post->content .'<br>';
            // PARA SACAR DATOS DE UNA TABLA RELACIONAL
            echo $post->user->name . '<hr>';
        }

        dd($post);
    }
}
