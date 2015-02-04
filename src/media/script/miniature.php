<?php
//namespace media\script\test ;

/*Script pour générer des miniatures d'images. Prend en paramètre le path de l'image à réduire (peut fonctionner avec une URL externe?),
 le path de la miniature générée et les x & y.
 Return true si le script s'est bien exécuté.
 Fonctionne avec les .gif .png .jpg
 TODO : .jpeg? */


function miniaturisation($origine, $destination, $x, $y){

	$path = $origine;
	$minia_path = $destination;

	$size = getimagesize($path);

	if ( $size) {

		//JPEG
		if ($size['mime']=='image/jpeg' ) {
			$img_big = imagecreatefromjpeg($path); # On ouvre l'image d'origine
			$img_new = imagecreate($x, $y);
			# création de la miniature
			$img_mini = imagecreatetruecolor($x, $y)
			or $img_mini = imagecreate($x, $y);
			// copie de l'image, avec le redimensionnement.
			imagecopyresampled($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagejpeg($img_mini,$minia_path );
			return true;
		}

		//PNG
		elseif ($size['mime']=='image/png' ) {
			$img_big = imagecreatefrompng($path); # On ouvre l'image d'origine
			$img_new = imagecreate($x, $y);
			# création de la miniature
			$img_mini = imagecreatetruecolor($x, $y)
			or $img_mini = imagecreate($x, $y);
			// copie de l'image, avec le redimensionnement.
			imagecopyresampled($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagepng($img_mini,$minia_path );
			return true;
		}

		//GIF
		elseif ($size['mime']=='image/gif' ) {
			$img_big = imagecreatefromgif($path); # On ouvre l'image d'origine
			$img_new = imagecreate($x, $y);
			# création de la miniature
			$img_mini = imagecreatetruecolor($x, $y)
			or $img_mini = imagecreate($x, $y);
			// copie de l'image, avec le redimensionnement.
			imagecopyresampled($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagegif($img_mini,$minia_path );
			return true;
		}
	}
}