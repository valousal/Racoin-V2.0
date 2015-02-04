<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Annonce extends Model {
	protected $table = 'annoncesracoin';
	protected $primaryKey = 'id';
	public $timestamps=false;


	/*public function annonceur(){
		return $this->belongsTo('media\modele\Annonceur', 'id_annonceurs');
	}*/

	public function images(){
		return $this->hasMany('media\modele\Images', 'id_annonce');
	}

	public function categorie(){
		return $this->belongsTo('media\modele\Categorie', 'id_categorie');
	}
}