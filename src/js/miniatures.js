$( "#miniatures img").click(function(){
	var src_min = $(this).attr('src');
	//var test = ""+src_min;
	var src_img = src_min.replace("img_annonces_miniatures", "img_annonces");
	var src_img = src_img.replace("_miniature", "");
	$('#main_img').attr('src', src_img);
	//alert(src_img);
});