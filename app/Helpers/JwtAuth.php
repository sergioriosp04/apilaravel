<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use App\User;

class JwtAuth
{
    public $key;

    public function __construct(){
        $this->key = 'clave_para_jwt';
    }

    public function signup($email, $password, $getToken = null){
        //buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        //comprobar si son correctas
        $signup = false;
        if(is_object($user)){
            $signup= true;
        }
        //generar el token con los datos del usuario identificad
        if($signup) {
            $token = array(
                'sub' => $user->id,  // el sub en jwt hace referencia al id del user
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),    // el sub en iat hace referencia al timpo en el que se creo el token
                'exp' => time() + (60 * 10)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256'); // INFORMACION DEL TOKEN, LLAVE SECRETA, Y METODO DE CODIFICACION OPCIONAL
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            //devolver los datos decodificados o el token, en funcion de un parametro
            if(is_null($getToken)){
                $data =  $jwt;
            }else{
                $data =  $decoded;
            }

        }else{
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto'
            ];
        }

        return $data;
    }
}
