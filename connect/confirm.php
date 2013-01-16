<?
require '../Init.php';

## --------------------------------------------------
##                   Confirmación
## --------------------------------------------------
## Página para darle la oportunidad al usuario
## de confirmar darle permisos a la aplicación en
## cuestión.
## --------------------------------------------------

# No ha iniciado sesión.
if(!LOG_IN)
	Core::Redirect();

# Obtenemos la información de la aplicación a partir de su clave pública.
$app = Apps::GetPublic($G['public']);

# ¡La aplicación no existe!
if(!$app)
	exit('Aplicación no encontrada.');

# ¿Que tiene de especial esta aplicación?
$features = json_decode($app['features'], true);

# Poner la informacion de la aplicación en variables de plantilla.
foreach($app as $key => $value)
	_t("app_$key", $value);

$page['id']			= 'confirm.app';
$page['folder']		= 'connect';
$page['subheader'] 	= 'login.header';
$page['login']		= true;
?>