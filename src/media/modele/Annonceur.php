<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Annonceur extends Model {
	protected $table = 'annonceursracoin';
	protected $primaryKey = 'id';
	public $timestamps=false;

	/*public function annonce(){
		return $this->hasMany('media\modele\Annonce', 'id_annonceurs');
	}*/
}