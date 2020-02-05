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
        // comprobar si el usuario esta autintificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        // recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if($checkToken && !empty($params_array)){
            // actualizar el usuario- pasos=

            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            //validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users'.$user->sub // que sea unico y haga relacion a la tabla users
            ]);

            // quitar los campos que no se actualizaran
            unset($params_array['id']);
            unset($params_array['password']);
            unset($params_array['role']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            //actualizar usuarip
            $user_update = User::where('id', $user->sub)->update($params_array);
            $params_json = json_encode($params_array);
            $params_json = json_decode($params_json);
            //retornar resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $params_json
            );


        }else{
            // sacar el error
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado correctamente'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //metodo para subir avatar
    public function upload(Request $request){
        //recoger los datos de la peticion
        $image = $request->file('file0');

        // validacion de imagen con validate
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image'
        ]);

        //guardar imagen
        if(!$image || $validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        }else{
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        return response()->json($data, $data['code']);
    }
}
