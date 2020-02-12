<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api.auth', ['except'=>['index', 'show']]);
    }

    public function index(){
        $categories = Category::all();
        return \response()->json([
            'code' => 200,
            'status'=> 'success',
            'categories' =>$categories
        ]);
    }

    public function show($id){
        $category = Category::find($id);
        if(is_object($category)){
            $data = [
                'code' => 200,
                'status'=> 'success',
                'categories' =>$category
            ];
        }else{
            $data = [
                'code' => 404,
                'status'=> 'error',
                'message' => 'la categorias con id:'. $id . ' no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request){
        //recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if($params_array){
            // validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required',
            ]);

            // guardar category
            if($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            }else{
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            }
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ningun dato'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
        // recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //validar los datos
        if(!empty($params_array)){
            $validator = \Validator::make($params_array, [
                'name' => 'required'
            ]);

        //quitar lo que no se va actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

        //actualizar el registro
            $category = Category::where('id', $id)->update($params_array);
            $data = [
                'code' => 200,
                'status' => ' success',
                'category' =>  'categoria actualizada con exito'
            ];


        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ningun dato'
            ];
        }


        //devolver respuesta
        return \response()->json($data, $data['code']);
    }
}
