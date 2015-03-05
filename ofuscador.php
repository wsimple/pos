<?php 
function ofuscar($string, $count=1){
	$result = array();
	$arreglo = str_split($string); //Separamos palabra por caracter

	for ($i=0; $i < $count; $i++) {
		$retorno = '';
		foreach ($arreglo as $value) {
			$paow = rand(0,2);
			if ($paow) {
				$retorno .= '\\'.decoct( ord( $value ) );
			}else{
				$retorno .= '\\x'.dechex( ord( $value ) );
			}
		}
		$result[$i]['original'] = $string;
		$result[$i]['ofus'] = $retorno;
	}
	return $result;
}
?>
<?php if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
	$word = $_POST['word'];
	$count = $_POST['count'];
	die( json_encode( ofuscar($word, $count) ) );
?>
<?php else: ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ofuscador de config</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
	<p>Saludos, Mihabri Hernandez. Este es el ofuscador especialmente creado para ti con amor.</p>
	<form action="ofuscador.php" method="POST" id="form-ofuscador">
		<label for="word">Palabra a ofuscar</label><input type="text" name="word">
		<label for="">Numero de veces a ofuscar</label>
		<select name="count">
			<?php foreach (range(1, 5) as $value): ?>
				<option value="<?php echo $value ?>"><?php echo $value ?></option>
			<?php endforeach ?>
		</select>
		<input type="submit" id="ofuscar" value="Ofuscar">
	</form>
	<p id="result"></p>
</body>
</html>
<script type="text/javascript">
	$('#ofuscar').click(function(event) {
		console.log('ofuscando...');
		var form = $('#form-ofuscador');
		$.ajax({
			url: form.action,
			type: 'POST',
			dataType: 'json',
			data: form.serialize(),
			success: function(response){
				$('#result').text('Palabra Original: '+response[0].original);
				for (var i = 0; i < response.length; i++) {
					$('#result').append('<p>Alternativa'+(i+1)+': "'+response[i].ofus+'";</p>');
				}
			}
		});
		return false;
	});
</script>
<?php endif ?>