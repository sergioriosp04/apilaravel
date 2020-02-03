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
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);

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
            echo $jwtAuth->signup();

            return "login accion";
        }
}
