<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Model;

class User extends Model{
	protected $table = "user";
	protected $primaryKey = "id";
	public $timestamps = false;
	protected $fillable = [
	    'nom',
        'prenom',
        'mail',
        'mdp',
        'level',
        'dateNaissance',

    ];
}