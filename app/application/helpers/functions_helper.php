<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('formatString')) {
	function formatString($st,$type=1)
	{
		switch($type){
			case 1:return ucwords($st);break;#Pone en mayusculas el primer caracter de cada palabra de una cadena
			case 2:return ucfirst($st);break;#Pasar a mayusculas el primer caracter de una cadena
			case 3:return strtolower($st);break;#Pasa a minusculas una cadena
			case 4:return strtoupper($st);break;#Pasa a mayusculas una cadena
		}
	}
}

if (!function_exists('formatDate')) {
	function formatDate($date,$type=1)
	{
		$aux = explode(' ', $date);
		switch($type){
			case 1: return date("d/m/Y", strtotime($aux[0])).' '.$aux[1]; break;
		}
	}
}

if (!function_exists('_imprimir')) {
	function _imprimir($array='',$die=false)
	{
		if($array=='') $array=$_REQUEST;
		echo '<pre>';print_r($array);echo '</pre>';
		if($die) die();
	}
}

if (!function_exists('getPosSplit')) {
	function getPosSplit($st,$pos=0,$char='.')
	{
		$array = explode ($char,$st);
		return $array[$pos];
	}
}

if (!function_exists('numberFormat')) {
	function numberFormat($num,$type=1)
	{
		switch ($type) {
			case 1:
				return number_format($num,2,',','.');
			break;
		}
	}
}

if (!function_exists('showDate')) {
	function showDate($date)
	{
		$date = explode ('-', $date);
		return $date[2].' / '.$date[1].' / '.$date[0];
	}
}

if (!function_exists('emailSetting')) {
	function emailSetting()
	{
		$config['protocol'] = 'mail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = true;
		$config['mailtype'] = 'html';
		$config['priority'] = 5;
		$config['charset'] = 'utf-8';

		return $config;
	}
}

if (!function_exists('comments')) {
	function comments($shortname='websarrollo')
	{
		return "
			<div id='disqus_thread'></div>
			<script type='text/javascript'>
			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			var disqus_shortname = '".$shortname."'; // required: replace example with your forum shortname
			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			})();
			</script>
			<noscript>Please enable JavaScript to view the <a href='http://disqus.com/?ref_noscript'>Cargando comentarios ...</a></noscript>
		";
	}
}

if (!function_exists('getCkEditorToolbar')) {
	function getCkEditorToolbar($advanced=false)
	{

		$insert = "
			{
				name: 'insert', 
				items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ]
			},
		";

		return "
			{  
				name: 'document', groups: [ 'mode', 'document', 'doctools' ], 
				items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ]  
			},
			{
				name: 'clipboard', groups: [ 'clipboard', 'undo' ], 
				items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ]
			},
			{
				name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], 
				items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ]
			},
			{
				name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ]
			},
			{ 
				name: 'tools', 
				items: [ 'Maximize', 'ShowBlocks' ] 
			},
			'/',
			{
				name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], 
				items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ]
			},
			{
				name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], 
				items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ]
			},
			{ 
				name: 'colors', 
				items: [ 'TextColor', 'BGColor' ] 
			},
			'/',
	
			".($advanced?$insert:'')."

			{ 
				name: 'styles', 
				items: [ 'Styles', 'Format', 'Font', 'FontSize' ] 
			}		
		";
	}
}

if (!function_exists('new_directory')) {
	function new_directory($path)
	{
		if (!is_dir($path)){
			$old=umask(0);
			mkdir($path,0777);
			umask($old);
			$fp=fopen($path.'index.html','w');
			fclose($fp);
		}
	}
}

if (!function_exists('upload_file')) {
	function upload_file($path, $file, &$error, &$photo)
	{
		$ci = get_instance();
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		// $config['max_size']	= '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		$config['overwrite']  = true;
		$config['file_name']  = md5(mt_rand(1, 9999999)*microtime());

		$ci->load->library('upload', $config);
		$ci->upload->initialize($config);

		$photo = array();

		try{ 
			if ($ci->upload->do_upload($file))
				$photo = $ci->upload->data();
			else
				$error = $ci->upload->display_errors('', '');
		}catch (Exception $e) {
			$error = $ci->upload->display_errors('', '').', Exception: '.$e;
		}

		//$photo = $photo;
		
		return count($photo) > 0 ? true : false;	
	}
}

if (!function_exists('resize_image')) {
	function resize_image($image, $width, $height=0)
	{
		$ci = get_instance();
		$img_nueva_anchura = $width;
		$img_nueva_altura = $height;

		list ($img_original_anchura, $img_original_altura) = getimagesize($image);
		
		if ($img_original_anchura > $img_nueva_anchura && $img_nueva_anchura > 0)
			$percent = (double)(($img_nueva_anchura * 100) / $img_original_anchura);

		if ($img_original_anchura <= $img_nueva_anchura)
			$percent = 100;

		if (floor(($img_original_altura * $percent )/100) > $img_nueva_altura && $img_nueva_altura > 0)
			$percent = (double)(($img_nueva_altura * 100) / $img_original_altura);

		$img_nueva_anchura = ($img_original_anchura*$percent)/100;
		$img_nueva_altura = ($img_original_altura*$percent)/100;

		$config['image_library'] = 'gd2';
		$config['source_image']	= $image;
		$config['create_thumb'] = false;
		$config['maintain_ratio'] = false;
		$config['width'] = intval($img_nueva_anchura);
		$config['height'] = intval($img_nueva_altura);
		$config['quality'] = '100%';

		$ci->load->library('image_lib', $config); 
		$ci->image_lib->resize();

		$ci->image_lib->clear();
	}
}

if (!function_exists('get_language')) {
	function get_language($print=false)
	{
		if ($print) 
			_imprimir($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

		switch (substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2)) {
			case 'en':
				return 'english';
			break;

			case 'es':
				return 'spanish';
			break;

			default:
				return 'english';
			break;
		}
	}
}

if (!function_exists('sigJsonToImage')) {
	function sigJsonToImage ($json, $options = array()) {
	  $defaultOptions = array(
	    'imageSize' => array(800, 100) //55
	    ,'bgColour' => array(0xff, 0xff, 0xff)
	    ,'penWidth' => 2
	    ,'penColour' => array(0x14, 0x53, 0x94)
	    ,'drawMultiplier'=> 12
	  );

	  $options = array_merge($defaultOptions, $options);

	  $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);

	  if ($options['bgColour'] == 'transparent') {
	    imagesavealpha($img, true);
	    $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
	  } else {
	    $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
	  }

	  $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
	  imagefill($img, 0, 0, $bg);

	  if (is_string($json))
	    $json = json_decode(stripslashes($json));

	  foreach ($json as $v)
	    drawThickLine($img, $v->lx * $options['drawMultiplier'], $v->ly * $options['drawMultiplier'], $v->mx * $options['drawMultiplier'], $v->my * $options['drawMultiplier'], $pen, $options['penWidth'] * ($options['drawMultiplier'] / 2));

	  $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);

	  if ($options['bgColour'] == 'transparent') {
	    imagealphablending($imgDest, false);
	    imagesavealpha($imgDest, true);
	  }

	  imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
	  imagedestroy($img);

	  return $imgDest;
	}
}

if (!function_exists('drawThickLine')) {
	function drawThickLine ($img, $startX, $startY, $endX, $endY, $colour, $thickness) {
	  $angle = (atan2(($startY - $endY), ($endX - $startX)));

	  $dist_x = $thickness * (sin($angle));
	  $dist_y = $thickness * (cos($angle));

	  $p1x = ceil(($startX + $dist_x));
	  $p1y = ceil(($startY + $dist_y));
	  $p2x = ceil(($endX + $dist_x));
	  $p2y = ceil(($endY + $dist_y));
	  $p3x = ceil(($endX - $dist_x));
	  $p3y = ceil(($endY - $dist_y));
	  $p4x = ceil(($startX - $dist_x));
	  $p4y = ceil(($startY - $dist_y));

	  $array = array(0=>$p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
	  imagefilledpolygon($img, $array, (count($array)/2), $colour);
	}
}

if (!function_exists('get_status')) {
	function get_status ($status) {
		switch ($status) {
			case '1':
				return 'Open';
			break;
			case '2':
				return 'In Process';
			break;
			case '3':
				return 'Competed';
			break;
			case '4':
				return 'Canceled';
			break;
			case '100':
				return 'Billed';
			break;
		}
	}
}


?>