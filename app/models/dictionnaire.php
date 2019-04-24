<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Model;
class Dictionnaire extends Model{
	protected $table = "dictionnaire";
	protected $primaryKey = "id";
	public $timestamps = false;
}
