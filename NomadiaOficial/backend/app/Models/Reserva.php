<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $fillable = [ 'experiencia_id', 'user_name', 'date', 'seats', 'status' ];
}
