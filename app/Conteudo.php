<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conteudo extends Model
{
    protected $fillable = [
        'titulo', 'texto', 'imagem','link','data'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function comentarios()
    {
        return $this->hasMany('App\Comentario');
    }

    public function curtidas(){
        return $this->belongsToMany('App\User', 'curtidas','conteudo_id','user_id');
    }

    public function getDataAttribute($value){
        $data = date('H:i d/m/Y',strtotime($value));
        return str_replace(':','h',$data);
    }
}
