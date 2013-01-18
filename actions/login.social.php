<?
require '../Init.php';

## --------------------------------------------------
## Inicio de sesión con un servicio social.
## --------------------------------------------------
## Esta página inicia sesión o registra al usuario con
## un servicio social (Facebook, Twitter o Steam)
## --------------------------------------------------

# Ya hemos iniciado sesión.
if(LOG_IN)
	Core::Redirect();

# Servicios permitidos.
$allowed = array('facebook', 'twitter', 'steam');

# ¿El usuario intento crear su propio servicio?
if( !in_array($G['type'], $allowed) )
	Core::Redirect();

# Obtener la información del usuario.
# Nota: Todo el sistema de las API ya esta programado en BeatRock.
$info 	= Social::Get($G['type']);
# ¿El usuario ya se registro con este servicio?
$verify = Users::ServiceExist($info['id'], $G['type']);

# Al parecer no, registrarlo.
if(!$verify)
{
	# Aquí estara el nombre de usuario final.
	$username = $info['username'];

	# Al parecer ya hay un usuario que tiene el mismo nombre de usuario.
	# Poner el servicio al principio del nombre actual. Ejemplo: facebook_kolesias123
	# TODO: Sinceramente no creo que esto sea lo mejor.
	if(Users::Exist($info['username'], 'username'))
		$username = $G['type'] . '_' . $username;

	# Debido a que los usuarios podrán "aliar" sus servicios a su cuenta, el hash sirve para identificar que servicios son de tal cuenta.
	$hash 	= Users::NewService($info['id'], $G['type'], $info['username'], json_encode($info));
	# Registrar usuario.
	$userId = Users::NewUser($username, '', $info['name'], $info['email'], $info['birthday'], '', true, array(
		'emails'		=> '[]',
		'country'		=> $info['country'],
		'gender'		=> $info['gender'],
		'privacy'		=> '{"email":"private","birthday":"private","lastaccess":"public","os":"private","country":"private"}',
		'service_hash'	=> $hash
	));

	# Al parecer la foto de perfil es una url, subirla a nuestro servidor.
	if(Core::Valid($info['profile_image_url'], 'url'))
		AcUsers::SaveUrlPhoto($info['profile_image_url'], $userId);
}
else
{
	# Ya esta registrado, solo iniciar sesión.
	Social::Login($G['type'], $info);	
}

# Redireccionar a la Home.
Core::Redirect();
?>