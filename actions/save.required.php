<?
$page['id'] 	= 'required';
require '../Init.php';

# No hemos iniciado sesión.
# Redireccionar a la página principal.
if(!LOG_IN)
	Core::Redirect();

$error 	= array();
$data 	= array();

if( empty($me['name']) OR empty($me['firstname']) OR empty($me['lastname']) )
{
	$data['name'] = true;

	// Nombre
	if(empty($P['firstname']))
		$error[] 	= 'Por favor escribe tu nombre.';

	// Apellidos
	if(empty($P['lastname']))
		$error[] 	= 'Por favor escribe tus apellidos.';
}

if(empty($me['birthday']))
{
	$data['birthday'] = true;

	// Fecha de nacimiento
	if($P['bday'] < 1 OR $P['bday'] > 31 OR !is_numeric($P['bday']))
		$error[] 	= 'El día es inválido, debes escribir un número de dos cifras.';

	if($P['bmonth'] < 1 OR $P['bmonth'] > 12 OR !is_numeric($P['bmonth']))
		$error[] 	= 'El mes es inválido, debes seleccionar uno de la lista.';

	if($P['byear'] > (date('Y') - 10))
		$error[] 	= 'Lo sentimos, eres muy joven para registrarte.';

	if($P['byear'] < (date('Y') - 90) OR !is_numeric($P['byear']))
		$error[] 	= 'El año es inválido, debes escribir un número de cuatro cifras.';
}

if(empty($me['email']))
{
	$data['email'] = $P['email'];

	// Correo electrónico
	if(!Core::Valid($P['email']))
		$error[]	= 'Por favor escribe un correo electrónico válido.';

	else if(Users::Exist($P['email']))
		$error[] 	= 'Tu correo electrónico ya esta siendo ocupado por otra cuenta.';
}

if(empty($me['gender']))
{
	$data['gender'] = $P['gender'];

	// Sexo
	if($P['gender'] !== 'm' AND $P['gender'] !== 'f')
		$error[] 	= 'Por favor selecciona un sexo válido.';
}

if(empty($me['country']))
{
	$data['country'] = $P['country'];

	// Ubicación
	if(empty($P['country']) OR strlen($P['country']) > 2)
		$error[] 	= 'Por favor selecciona tu ubicación';
}

if(empty($error))
{
	if($data['name'] == true)
	{
		$data['name'] 		= $P['firstname'] . ' ' . $P['lastname'];
		$data['firstname']	= $P['firstname'];
		$data['lastname']	= $P['lastname'];
	}

	if($data['birthday'] == true)
		$data['birthday'] = $P['bday'] . '/' . $P['bmonth'] . '/' . $P['byear'];

	Users::Update($data);
	Core::Redirect();
}
else
{
	$message = '';

	foreach($error as $e)
	{
		$e 			= htmlentities($e);
		$message	= "<li>$e</li>";
	}

	_SESSION('required_errors', $message);
	_SESSION('required_data', $P);

	Core::Redirect('/required');
}
?>