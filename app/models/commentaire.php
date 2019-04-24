<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Model;

class Commentaire extends Model{
	protected $table = "commentaire";
	public $incrementing = false;
	public $timestamps = false;
}