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
            $user = $this->getIdentity($request);

        //validar los datos
            $validate = \Validator::make($params_array, [
               'title' => 'required',
               'content' => 'required',
               'category_id' => 'required',
                'image' => 'required'
            ]);

            if($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            }else{
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Se ha guardado un nuevo post con exito',
                    'post' => $post
                ];
            }

        //guardar el post
        //devolver respuesta
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha guardado el post, no llegaron datos'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
        //recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
            // validar los dtos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if($validate->fails()){
                $data=[
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            }else{
                //eliminar lo que no se actualizara
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);

                //conseguir usuario identificado
                $user = $this->getIdentity($request);

                //conseguir el post
                $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

                if(!empty($post) && is_object($post)){
                    $post->update($params_array);
                    $data = [
                        'code' => 404,
                        'status' => 'error',
                        'post' => $post
                    ];

                }else{
                    $data=[
                        'code' => 404,
                        'status' => 'error',
                        'post' => 'el post no existe para el ususario'
                    ];
                }

                /*//actualizar el registro
                $where = [
                    'id' => $id,
                    'user_id' => $user->sub
                ];
                $post = Post::updateOrCreate($where, $params_array);
                $data=[
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'post actualizado con exito',
                    'post' => $post
                ];*/
            }

        }else{
            $data=[
                'code' => 400,
                'status' => 'error',
                'post' => 'no se enviaron datos o son incorrectos'
            ];
        }

        //retornar respuesta
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request){
        $user = $this->getIdentity($request);
        //conseguir el post
        $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

        if(!empty($post)){
            //borrarlo
            $post->delete();

            //devolver algo
            $data = [
                'code'=> 200,
                'status'=> 'success',
                'message' => 'post borrado on exito',
                'post'=> $post
            ];
        }else{
            $data = [
                'code'=> 404,
                'status'=> 'error',
                'message' => 'el post con id: '. $id .'no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request){
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checktoken($token, true);

        return $user;
    }
}
