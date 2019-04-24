<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Model;
class Sequence extends Model{
	protected $table = "sequence";
	protected $primaryKey = "id";
	public $timestamps = false;
}