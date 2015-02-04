<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class ClientApi extends Model {
	protected $table = 'client_apiracoin';
	protected $primaryKey = 'id';
	public $timestamps=false;

	
}
