<?
require '../Init.php';

## --------------------------------------------------
## Registro
## --------------------------------------------------
## Página para procesar el registro del formulario.
## --------------------------------------------------

# Ya hemos iniciado sesión.
if(LOG_IN)
	Core::Redirect();

$error 		= array();
# Verificar si el código captcha es correcto.
$captcha 	= Captcha::Verify(); 
# Una llave que aumenta un poco la seguridad del registro.
$key 		= json_decode(base64_decode($P['key']), true);

# Encerio, no necesitamos esto...
unset($key['key']);

# Verificar que los valores de la llave coinciden con el del registro.
# Si ves esto desde Github... shhh!
foreach($key as $param => $value)
{
	# Algo no coincide ¿Intento de un hack? :yaoming:
	if($PA[$param] !== $key[$param])
	{
		# No le diremos directamente que fue ¿cierto?
		$error[] = 'Ha ocurrido un problema interno, vuelve a intentarlo.';
		break;
	}
}

# Nombre
# TODO: Una verificación mejor.
if(empty($P['firstname']))
	$error[] 	= 'Por favor escribe tu nombre.';

# Apellidos
# TODO: Una verificación mejor.
if(empty($P['lastname']))
	$error[] 	= 'Por favor escribe tus apellidos.';

# Nombre de usuario
if(strlen($P['username']) < 5)
	$error[] 	= 'Tu nombre de usuario es muy corto, intentalo de nuevo con al menos 5 caracteres.';

else if(!Core::Valid($P['username'], 'username'))
	$error[] 	= 'Por favor utiliza solo letras (a-z) y números para tu nombre de usuario.';

else if(Users::Exist($P['username'], 'username'))
	$error[] 	= 'Tu nombre de usuario ya esta siendo ocupado, escoje otro.';

# Contraseña
if(strlen($P['password']) < 8)
	$error[] 	= 'Tu contraseña es muy sencilla, intentalo de nuevo con al menos 8 caracteres.';

else if(!Core::Valid($P['password'], 'password'))
	$error[] 	= 'Por favor utiliza solo letras (a-z) y números para tu contraseña.';

if($P['password'] !== $P['confirm_password'])
	$error[] 	= 'Tus contraseñas no coinciden, vuelve a intentarlo.';

# Fecha de nacimiento
if($P['bday'] < 1 OR $P['bday'] > 31 OR !is_numeric($P['bday']))
	$error[] 	= 'El día es inválido, debes escribir un número de dos cifras.';

if($P['bmonth'] < 1 OR $P['bmonth'] > 12 OR !is_numeric($P['bmonth']))
	$error[] 	= 'El mes es inválido, debes seleccionar uno de la lista.';

if($P['byear'] > (date('Y') - 10))
	$error[] 	= 'Lo sentimos, eres muy joven para registrarte.';

if($P['byear'] < (date('Y') - 90) OR !is_numeric($P['byear']))
	$error[] 	= 'El año es inválido, debes escribir un número de cuatro cifras.';

# Correo electrónico
if(!Core::Valid($P['email']))
	$error[]	= 'Por favor escribe un correo electrónico válido.';

else if(Users::Exist($P['email']))
	$error[] 	= 'Tu correo electrónico ya esta siendo ocupado por otra cuenta.';

# Sexo (Genero)
if($P['gender'] !== 'm' AND $P['gender'] !== 'f')
	$error[] 	= 'Por favor selecciona un sexo válido.';

# Ubicación
if(empty($P['country']) OR strlen($P['country']) > 2)
	$error[] 	= 'Por favor selecciona tu ubicación';

# Captcha
# TODO: Me da un error el controlador de Captcha, después lo soluciono...
//if($captcha == false)
//	$error[] 	= 'El código de seguridad es incorrecto, intentalo de nuevo.';

# Sin errores.
if( empty($error) )
{
	# Juntamos el nombre y apellidos.
	$name 		= $P['firstname'] . ' ' . $P['lastname'];
	# Ponemos este formato de fecha de nacimiento.
	# TODO: ¿Algo mejor que esto?
	$birthday 	= $P['bday'] . '/' . $P['bmonth'] . '/' . $P['byear'];

	# Registrar usuario.
	Users::NewUser($P['username'], $P['password'], $name, $P['email'], $birthday, array(), true, array(
		'firstname' 	=> $P['firstname'],
		'lastname'		=> $P['lastname'],
		'emails'		=> '[]',
		'country'		=> $P['country'],
		'gender'		=> $P['gender'],
		'privacy'		=> '{"email":"private","birthday":"private","lastaccess":"public","os":"private","country":"private"}'
	));

	# Vamos a la home.
	# TODO: Hacer página de "Binvenida".
	Core::Redirect();
}
else
{
	# ¡Uy! Un error.
	$message = '';

	# Juntamos todos los errores en un solo mensaje separado por <li>
	foreach($error as $e)
	{
		# Evitamos errores de caracteres.
		$e 			= htmlentities($e);
		$message 	.= "<li>$e</li>";
	}

	# Guardamos los errores y la información del formulario en sesiones.
	_SESSION('register_errors', $message);
	_SESSION('register_data', $P);

	# Redireccionamos al registro de nuevo.
	Core::Redirect('/connect/register');
}
?>