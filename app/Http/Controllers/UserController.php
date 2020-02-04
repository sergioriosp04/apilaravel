<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    //
    public function pruebas(Request $request){
        return "accion de pruebas de user controller";
    }

    public function register(Request $request){
        // recoger los datos del ususarios ppr post
        $json = $request->input('json', null);

        // decodificar el json string a un array asociativo
        $params = json_decode($json); // object
        $params_array = json_decode($json, true); // convertido a array

        // iniciar validacion encaso de que no llegue vacia por cualquier motivo
        if(!empty($params_array)){
            //limpiar datos
            $params_array = array_map('trim', $params_array);

            //validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', // que sea unico y haga relacion a la tabla users
                'password' => 'required'
            ]);

            // si hay errors en validacion
            if($validate->fails()){
                // validacion fallo
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'el usuario no se ha creado',
                    'errors' => $validate->errors()
                ];

            }else{
                // validacion correctamente
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'el usuario se ha creado correctamente',
                ];

                // cifrar la contraseña con password_hash
                $pwd = hash( 'sha256', $params->password );

                // crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                // guardar el usuario
                $user->save();
            }
        }else{
            $data = [
                'status' => 'success',
                'code' => 400,
                'message' => 'los datos enviados no son correctos',
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();

        //recibir datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //validar datos
        $validate = \Validator::make($params_array, [
            'email' => 'required|email', // que sea unico y haga relacion a la tabla users
            'password' => 'required'
        ]);

        // si hay errors en validacion
        if($validate->fails()){
            // validacion fallo
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'el usuario no se ha podido identifiacar',
                'errors' => $validate->errors()
            ];
        }else{
            //cifrar contraseña
            $pwd = hash('sha256', $params->password);

            //devolver datos
            $signup =  $jwtAuth->signup($params->email, $pwd);

            if(!empty($params->gettoken)){
                $signup =  $jwtAuth->signup($params->email, $pwd, true);
            }

        }
        return response()->json($signup, 200);
    }

    public function update(Request $request){
        $token = $request->header('Authorization');
        //dd($token);
        //$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMsImVtYWlsIjoiam9yZ2VAam9yZ2UuY29tIiwibmFtZSI6ImpvcmdlIiwic3VybmFtZSI6InJpb3MiLCJpYXQiOjE1ODA4MzYwNDIsImV4cCI6MTU4MDgzNjY0Mn0.ZsW3UJWFJeDFrZlLasVAhct93ONOhB5c6uqKy9y0MeM';
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            echo "login correcto";
        }else{
            echo "login incorrecto";
        }
        die();
    }
}
