<?
$page['require_login'] = true;
require '../../Init.php';

## --------------------------------------------------
##     Guardado de información en tiempo real
## --------------------------------------------------
## Esta página es solicitada por AJAX cuando el usuario
## cambia su información.
## --------------------------------------------------

$type 	= $P['type'];
$value 	= $P['value'];

if($P['type'] == 'firstname')
{
	if(empty($P['value']))
		$error = 'Por favor escribe tu nombre.';

	$value 	= array(
		'name'		=> $P['value'] . ' ' . $me['lastname'],
		'firstname'	=> $P['value']
	);
}

if($P['type'] == 'lastname')
{
	if(empty($P['value']))
		$error = 'Por favor escribe tus apellidos.';

	$value = array(
		'name'		=> $me['firstname'] . ' ' . $P['value'],
		'lastname'	=> $P['value']
	);
}

if($P['type'] == 'profile')
{
	$value = '1';

	if(empty($P['value']))
		$value = '0';
}

if($P['type'] == 'country')
{
	if(empty($P['value']))
		$error = 'Selecciona una ubicación de la lista.';
}

if($P['type'] == 'gender')
{
	if($P['value'] !== 'm' AND $P['value'] !== 'f')
		$error = 'Por favor selecciona un sexo válido.';
}

if($P['type'] == 'secure')
{
	$value = '1';

	if(empty($P['value']))
		$value = '0';
}

if($P['type'] == 'magic_word')
{
	if(strlen($P['value']) > 50)
		$error = 'La palabra mágica debe tener menos de 50 caracteres.';
}

if(empty($error))
{
	if(is_array($value))
		Users::Update($value);
	else
		Users::UpdateData($type, $value);

	$result['status'] = 'OK';
}
else
{
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
}

echo json_encode($result);
?>