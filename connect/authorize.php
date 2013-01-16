<?
require '../Init.php';

## --------------------------------------------------
##               Autorización
## --------------------------------------------------
## Verifica la información del usuario y lo 
## redirecciona a la página de inicio de sesión o
## confirmación.
## Si ya ha iniciado sesión y ha confirmado solo genera
## y retorna la llave de autorización.
## --------------------------------------------------

# Parametros a pasar por la dirección.
$params = array(
	'public'	=> $R['public'],
	'return'	=> $RA['return']
);
$params = '?' . http_build_query($params);

# Obtenemos la información de la aplicación a partir de su clave pública.
$app = Apps::GetPublic($R['public']);

# ¡La aplicación no existe!
if(!$app)
	exit('Aplicación no encontrada.');

# No se ha iniciado sesión, redireccionar a la página de inicio de sesión.
if(!LOG_IN)
	Core::Redirect(PATH . '/connect/login' . $params);

# Se ha autorizado, agregar a la lista blanca del usuario.
# TODO: Hacer algo mejor que esto.
if($G['authorize'] == 'true')
{
	Apps::Authorize($app['id']);
}

# ¿La aplicación ya esta autorizada/confirmada para usar la info del usuario?
$ready = Apps::Authorized($app['id']);

# No aún no, redireccionar a la página de confirmación.
if(!$ready)
{
	Core::Redirect(PATH . '/connect/confirm' . $params);
}

# TODO BIEN
# Generar la llave de autorización y regresarla.
$key 	= API::NewAuthorizeKey($app['id']);
$return = urldecode($RA['return']);
$ext 	= (Contains($return, '?')) ? '&' : '?';

Core::Redirect($return . $ext . 'authorize=' . $key);
?>