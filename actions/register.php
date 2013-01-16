<?
require '../Init.php';

if(LOG_IN)
	Core::Redirect();

$error 		= array();
$captcha 	= Captcha::Verify(); 
$key 		= json_decode(base64_decode($P['key']), true);

unset($key['key']);

// Clave de seguridad
foreach($key as $param => $value)
{
	if($PA[$param] !== $key[$param])
	{
		$error[] = 'Ha ocurrido un problema interno, vuelve a intentarlo.';
		break;
	}
}

// Nombre
if(empty($P['firstname']))
	$error[] 	= 'Por favor escribe tu nombre.';

// Apellidos
if(empty($P['lastname']))
	$error[] 	= 'Por favor escribe tus apellidos.';

// Nombre de usuario
if(strlen($P['username']) < 5)
	$error[] 	= 'Tu nombre de usuario es muy corto, intentalo de nuevo con al menos 5 caracteres.';

else if(!Core::Valid($P['username'], 'username'))
	$error[] 	= 'Por favor utiliza solo letras (a-z) y números para tu nombre de usuario.';

else if(Users::Exist($P['username'], 'username'))
	$error[] 	= 'Tu nombre de usuario ya esta siendo ocupado, escoje otro.';

// Contraseña
if(strlen($P['password']) < 8)
	$error[] 	= 'Tu contraseña es muy sencilla, intentalo de nuevo con al menos 8 caracteres.';

else if(!Core::Valid($P['password'], 'password'))
	$error[] 	= 'Por favor utiliza solo letras (a-z) y números para tu contraseña.';

if($P['password'] !== $P['confirm_password'])
	$error[] 	= 'Tus contraseñas no coinciden, vuelve a intentarlo.';

// Fecha de nacimiento
if($P['bday'] < 1 OR $P['bday'] > 31 OR !is_numeric($P['bday']))
	$error[] 	= 'El día es inválido, debes escribir un número de dos cifras.';

if($P['bmonth'] < 1 OR $P['bmonth'] > 12 OR !is_numeric($P['bmonth']))
	$error[] 	= 'El mes es inválido, debes seleccionar uno de la lista.';

if($P['byear'] > (date('Y') - 10))
	$error[] 	= 'Lo sentimos, eres muy joven para registrarte.';

if($P['byear'] < (date('Y') - 90) OR !is_numeric($P['byear']))
	$error[] 	= 'El año es inválido, debes escribir un número de cuatro cifras.';

// Correo electrónico
if(!Core::Valid($P['email']))
	$error[]	= 'Por favor escribe un correo electrónico válido.';

else if(Users::Exist($P['email']))
	$error[] 	= 'Tu correo electrónico ya esta siendo ocupado por otra cuenta.';

// Sexo
if($P['gender'] !== 'm' AND $P['gender'] !== 'f')
	$error[] 	= 'Por favor selecciona un sexo válido.';

// Ubicación
if(empty($P['country']) OR strlen($P['country']) > 2)
	$error[] 	= 'Por favor selecciona tu ubicación';

// Captcha
//if($captcha == false)
//	$error[] 	= 'El código de seguridad es incorrecto, intentalo de nuevo.';

if(empty($error))
{
	$name 		= $P['firstname'] . ' ' . $P['lastname'];
	$birthday 	= $P['bday'] . '/' . $P['bmonth'] . '/' . $P['byear'];

	Users::NewUser($P['username'], $P['password'], $name, $P['email'], $birthday, array(), true, array(
		'firstname' 	=> $P['firstname'],
		'lastname'		=> $P['lastname'],
		'emails'		=> '[]',
		'country'		=> $P['country'],
		'gender'		=> $P['gender'],
		'privacy'		=> '{"email":"private","birthday":"private","lastaccess":"public","os":"private","country":"private"}'
	));

	Core::Redirect();
}
else
{
	$message = '';

	foreach($error as $e)
	{
		$e 			= htmlentities($e);
		$message 	.= "<li>$e</li>";
	}

	_SESSION('register_errors', $message);
	_SESSION('register_data', $P);

	Core::Redirect('/connect/register');
}
?>