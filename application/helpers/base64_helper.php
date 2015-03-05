<?php
function decodeBase64($string){
	$Base64=$string;
	list(,$Base64) = explode(';', $Base64);
	list(,$Base64) = explode(',', $Base64);
	$Base64 = base64_decode($Base64);
	return $Base64;
}
function imagenBase64($path,$image){
	$Base64Img=decodeBase64($image);
	if (file_put_contents('images/'.$path, $Base64Img)) return true;
	return false; 
}
?>