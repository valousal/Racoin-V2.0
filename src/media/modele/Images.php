<?php
	
namespace media\modele;
use \Illuminate\Database\Eloquent\Model ;

class Images extends Model {
	protected $table = 'imagesracoin';
	protected $primaryKey = 'id';
	public $timestamps=false;

	public function annonce(){
		return $this->belongsTo('media\modele\Annonce', 'id_annonce');
	}
}