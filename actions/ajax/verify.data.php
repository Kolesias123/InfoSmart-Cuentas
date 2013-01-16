<?
$page['require_login'] = true;
require '../../Init.php';

$error = array();

if(!empty($P['username']))
{
	if(strlen($P['username']) < 5)
		$error['username'] 	= 'Tu nombre es muy corto, intentalo de nuevo con al menos 5 caracteres.';

	else if(!Core::Valid($P['username'], 'username'))
		$error['username'] 	= 'Por favor utiliza solo letras (a-z) y números.';
}

if(!empty($P['password']))
{
	if(strlen($P['password']) < 8)
		$error['password'] 	= 'Tu contraseña es muy sencilla, intentalo de nuevo con al menos 8 caracteres.';

	else if(!Core::Valid($P['password'], 'password'))
		$error['password'] 	= 'Por favor utiliza solo letras (a-z) y números.';

	if($P['password'] !== $P['confirm_password'])
		$error['confirm_password'] = 'Tu contraseña no coincide con la que has escrito.';
}

if(!empty($P['bday']))
{
	if($P['bday'] < 1 OR $P['bday'] > 31 OR !is_numeric($P['bday']))
		$error['bday'] 		= 'El día es inválido, debes escribir un número de dos cifras.';
}

if(!empty($P['bmonth']))
{
	if($P['bmonth'] < 1 OR $P['bmonth'] > 12 OR !is_numeric($P['bmonth']))
		$error['bmonth'] 	= 'El mes es inválido, debes seleccionar uno de la lista.';
}

if(!empty($P['byear']))
{
	if($P['byear'] > (date('Y') - 10))
		$error['byear'] 	= 'Lo sentimos, eres muy joven para registrarte.';

	if($P['byear'] < (date('Y') - 90) OR !is_numeric($P['byear']))
		$error['byear'] 	= 'El año es inválido, debes escribir un número de cuatro cifras.';
}

if(!Core::Valid($P['email']) AND !empty($P['email']))
	$error['email']			= 'Por favor escribe un correo electrónico válido.';

foreach($error as $key => $value)
	$error[$key] = htmlentities($value);

echo json_encode($error);
?>