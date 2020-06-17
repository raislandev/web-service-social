<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Auth;

class UsuarioController extends Controller
{
    public function login(Request $request){
        $data = $request->all();

        $validacao = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if($validacao->fails()){
            return ['status'=> false,'validacao' =>true,'erros'=>$validacao->errors() ]; 
        }

        if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->accessToken;
            //$user->imagem = asset($user->imagem);
            return ['status'=> true,'usuario'=> $user];
        }else{
            return ['status'=> false];
        }
    }


    public function cadastro(Request $request){
        $data = $request->all();

        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validacao->fails()){
            return ['status'=> false,'validacao' =>true,'erros'=>$validacao->errors() ];
        }

        $imagem = 'perfils/padrao.png';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'imagem' => $imagem,
        ]);
        //$user->imagem = asset($user->imagem);
        $user->token = $user->createToken($user->email)->accessToken;

        return ['status'=> true,'usuario'=> $user];
    }


    public function perfil(Request $request){
        $user =  $request->user();
        $data = $request->all();
        
        if(isset($data['password'])){
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validacao->fails()){
                return ['status'=> false,'validacao' =>true,'erros'=>$validacao->errors() ];
            }
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);

        }else{
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
            ]);
        
            if($validacao->fails()){
                return ['status'=> false,'validacao' =>true,'erros'=>$validacao->errors() ];
            }
            $user->name = $data['name'];
            $user->email = $data['email'];

        }

        if(isset($data['imagem'])){

            Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
                $explode = explode(',', $value);
                $allow = ['png', 'jpg', 'svg','jpeg'];
                $format = str_replace(
                    [
                        'data:image/',
                        ';',
                        'base64',
                    ],
                    [
                        '', '', '',
                    ],
                    $explode[0]
                );
                // check file format
                if (!in_array($format, $allow)) {
                    return false;
                }
                // check base64 format
                if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                    return false;
                }
                return true;
            });

            $valiacao = Validator::make($data, [
                'imagem' => 'base64image',

            ],['base64image'=>'Imagem inválida']);

            if($valiacao->fails()){
                return ['status'=> false,'validacao' =>true,'erros'=>$validacao->errors() ];
            }


            $time = time();
            $diretorioPai = 'perfils';
            $diretorioImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;
            $ext = substr($data['imagem'],11,strpos($data['imagem'],';')-11);
            $urlImagem= $diretorioImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;
            $file = str_replace('data:image/'.$ext.';base64,','',$data['imagem']);
            $file = base64_decode($file);
            

            if(!file_exists($diretorioPai)){
                mkdir($diretorioPai,0700);
            }

            if($user->imagem){
                $imgUser = str_replace(asset('/'),'',$user->imagem);
                if(file_exists($imgUser)){
                    unlink($imgUser);
                }
                
            }

            if(!file_exists($diretorioImagem)){
                mkdir($diretorioImagem,0700);
            }

            file_put_contents($urlImagem,$file);
            $user->imagem = $urlImagem;
        }

        $user->save();
        
        //$user->imagem = asset($user->imagem);
        $user->token = $user->createToken($user->email)->accessToken;
        return ['status'=> true,'usuario'=> $user];
    }

    public function amigo(Request $request){
        $user =  $request->user();
        $amigo = User::find($request->id);
        if($amigo){
            $user->amigos()->toggle($amigo->id);
            return ['status'=> true,'amigos'=> $user->amigos, 'seguidores' => $amigo->seguidores]; 
        }else{
            return ['status'=> false,'erro'=>  "usuário não existe!"]; 
        }
    }

    public function list_amigos(Request $request){
        $user = $request->user();
        if($user){
            return ['status'=> true,'amigos'=> $user->amigos, 'seguidores' => $user->seguidores];
        }else{
            return ['status'=> false,'erro'=> 'Esse usuário não existe'];
        }

    }

    public function list_amigos_pagina($id,Request $request){
        $userLogado = $request->user();
        $user = User::find($id);

        if($user){
            return ['status'=> true,'amigos'=> $user->amigos,'amigosLogado' => $userLogado->amigos, 'seguidores' => $user->seguidores];
            
        }else{
            return ['status'=> false,'erro'=> 'Esse usuário não existe'];
        }

    }

    public function list_geral_amigos($id,Request $request){
        $user = User::find($id);
        $id_amigos = [];
        foreach ($user->amigos as $key => $value) {
            array_push($id_amigos,$value->id);
        }
        array_push($id_amigos,$user->id);
        $users = User::whereNotIn('id',$id_amigos)->paginate(7);

        foreach ($users as $key => $value) {
            foreach ($user->seguidores as $key => $value2) {
                if($value->id == $value2->id){
                    $value['seguidor'] = true;
                }else{
                    $value['seguidor'] = false;
                }
            }
        }

        if($user){
            return ['status'=> true,'sugestao_amigos'=> $users];
            
        }else{
            return ['status'=> false,'erro'=> 'Esse usuário não existe'];
        }

    }
}
