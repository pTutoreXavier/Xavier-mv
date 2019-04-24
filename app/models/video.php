<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Model;
class Video extends Model{
	protected $table = "video";
	protected $primaryKey = "id";
	public $timestamps = false;
}
