<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experiencia extends Model
{
    protected $table = 'experiencias';
        protected $fillable = [
            'guia_id',
            'titulo',
            'descripcion',
            'precio',
            'categoria',
            'duracion',
            'estado',
        ];
    
        /**
         * Relación: experiencia pertenece a un guía (User)
         */
        public function guia()
        {
            return $this->belongsTo(User::class, 'guia_id');
        }
    
        /**
         * Relación: una experiencia tiene muchas disponibilidades
         */
        public function disponibilidad()
        {
            return $this->hasMany(Disponibilidad::class, 'experiencia_id');
        }
}
