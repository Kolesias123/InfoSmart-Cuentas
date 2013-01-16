<?
require '../Init.php';

## --------------------------------------------------
##                   Confirmación
## --------------------------------------------------
## Página para darle la oportunidad al usuario
## de confirmar darle permisos a la aplicación en
## cuestión.
## --------------------------------------------------

# Ya hemos iniciado sesión.
if(LOG_IN)
	Core::Redirect();

## Sistema en caso de error al iniciar sesión.
$error['field'] 	= _SESSION('login_error_field');
$error['message']	= _SESSION('login_error_message');
$loginId			= _SESSION('login_id');

if( !empty($error['message']) )
{
	 _DELSESSION('login_error_field');
	 _DELSESSION('login_error_message');
	 _DELSESSION('login_id');
}

if($error['field'] == 'id')
	$field_id 		= 'class="error"';
if($error['field'] == 'password')
	$field_password = 'class="error"';

# Al parecer es para iniciar sesión y confirmar el uso de nuestra info a una aplicación.
if( !empty($G['public']) )
{
	# Obtenemos la información de la aplicación a partir de su clave pública.
	$app = Apps::GetPublic($G['public']);

	# ¡La aplicación no existe!
	if(!$app)
		exit('Aplicación no encontrada');

	# Poner la informacion de la aplicación en variables de plantilla.
	foreach($app as $key => $value)
		_t("app_$key", $value);

	# Parametros a pasar por la dirección.
	$params = array(
		'public'	=> $G['public'],
		'return'	=> urlencode($GA['return']),
		'authorize'	=> 'true'
	);
	$params = '?' . http_build_query($params);

	# Esta es la página donde regresaremos al iniciar sesión.
	$return 		= PATH . '/connect/authorize' . $params;
	# Esta es la página donde regresaremos si ocurrio un error.
	$return_error 	= PATH . '/connect/login' . $params;
	# ¿Que tiene de especial esta aplicación?
	$features 		= json_decode($app['features'], true);
	# La plantilla especial para esta ocación.
	$page['id']		= 'login.app';
}
else
	$page['id']		= 'login';

$page['folder']		= 'connect';
$page['subheader'] 	= 'login.header';
$page['login']		= true;
?>