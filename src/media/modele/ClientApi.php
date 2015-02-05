<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class ClientApi extends Model {
	protected $table = 'client_api';
	protected $primaryKey = 'id';
	public $timestamps=false;

	
}
