<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\User;
use App\Comentario;
use App\Conteudo;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/cadastro', 'UsuarioController@cadastro');

Route::post('/login','UsuarioController@login');

Route::middleware('auth:api')->put('/perfil','UsuarioController@perfil');
Route::middleware('auth:api')->post('/usuario/amigo','UsuarioController@amigo');
Route::middleware('auth:api')->get('/usuario/listamigos','UsuarioController@list_amigos');
Route::middleware('auth:api')->get('/usuario/listamigoslogado/{id}','UsuarioController@list_amigos_pagina');
Route::middleware('auth:api')->get('/usuario/listgeralamigos/{id}','UsuarioController@list_geral_amigos');




Route::middleware('auth:api')->post('/conteudo/add','ConteudoController@adicionar');
Route::middleware('auth:api')->get('/conteudo/lista','ConteudoController@lista');
Route::middleware('auth:api')->put('/conteudo/curtir/{id}','ConteudoController@curtir');
Route::middleware('auth:api')->put('/conteudo/curtirPagina/{id}','ConteudoController@curtirPagina');
Route::middleware('auth:api')->post('/conteudo/comentar/{id}','ConteudoController@comentar');
Route::middleware('auth:api')->post('/conteudo/comentarPagina/{id}','ConteudoController@comentarPagina');

Route::middleware('auth:api')->get('/conteudo/page/lista/{id}','ConteudoController@pagina');




Route::get("/teste",function(){ 
    $user = User::find(1)->get();
    foreach ($user as $key => $value) {
        $value['teste'] = true;

    }
    dd($user);
    /*$id_amigos = [];
    foreach ($user->amigos as $key => $value) {
        array_push($id_amigos,$value->id);
    }
    array_push($id_amigos,$user->id);
    //dd($id_amigos);
    $users = User::whereNotIn('id',$id_amigos)->get();

    dd($users);*/
    /*$user = User::find(1);
    $user2 = User::find(2);
    $user->amigos()->toggle($user2->id);*/

    //$user = $request->user();
    /*$amigos = $user->amigos()->pluck('amigo_id');
    $amigos->push($user->id);
    dd($amigos);
    $conteudos = Conteudo::whereIn('user_id',$amigos)->with('user')->orderBy('data','DESC')->paginate(5);*/
 

    /*$conteudo = Conteudo::all();
    foreach ($conteudo as $key => $value) {
        $value->delete();
    }*/
    /*$user->conteudos()->create([
        'titulo' => 'conteudo2', 
        'texto' => 'texto', 
        'imagem'=> 'url da imagem',
        'link'=> 'link',
        'data'=> '2020-03-05'
    ]);
    return $user->conteudos;*/

    /*$user->amigos()->toggle($user2->id);
    return $user->amigos;*/

    /*$conteudo = Conteudo::find(1);
    $user->curtidas()->toggle($conteudo->id);
    return $user->curtidas;*/
    //return $conteudo->curtidas->count();*/

    
    /*$conteudo = Conteudo::find(13); 
    dd(Comentario::all());
    $user->comentarios()->create([
        'conteudo_id' => $conteudo->id, 
        'texto' => 'gostei de desse conteudo',
        'data'=> '2020-03-05'
    ]);*/

    /*$user->comentarios()->create([
        'conteudo_id' => $conteudo->id, 
        'texto' => 'texto',
        'data'=> '2020-03-05'
    ]);

    return $conteudo->comentarios;*/


});