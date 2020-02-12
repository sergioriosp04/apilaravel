<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api.auth', ['except' =>['index', 'show']]);
    }

    public function index(){
        $posts = Post::all()->load('category');
        return response()->json([
            'code' => 200,
            'status' => 'succes',
            'posts' => $posts
        ], 200);
    }

    public function show($id){
        $post = Post::find($id)->load('Category', 'User');
        if(is_object($post)){
            $data = [
                'code' => 200,
                'status' => 'succes',
                'posts' => $post
            ];
        }else{
            $data = [
                'code' =>404,
                'status' => 'error',
                'message' => 'la entrada no existe'
            ];
        }
        return \response()->json($data, $data['code']);
    }

    public function store(Request $request){
        //recoger datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
        //conseguir el usuario identificado
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization');
            $user = $jwtAuth($token, true);

        //validar los datos

        //guardar el post
        //devolver respuesta
        }else{

        }
    }
}
