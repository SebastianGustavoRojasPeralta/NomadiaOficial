<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    protected $table = 'disponibilidades';
    protected $fillable = [ 'experiencia_id', 'date', 'seats' ];
}
