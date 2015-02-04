<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Categorie extends Model {
	protected $table = 'categoriesracoin';
	protected $primaryKey = 'id';
	public $timestamps=false;


	public function annonces(){
		return $this->hasMany('media\modele\Annonce', 'id_categorie');
	}
}