<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    //
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
        
    }

}
