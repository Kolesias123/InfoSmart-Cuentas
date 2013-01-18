<?
require '../Init.php';

## --------------------------------------------------
## Desarrolladores - Guardar aplicación.
## --------------------------------------------------
## Guardar los cambios realizados a una aplicación.
## --------------------------------------------------

# No hemos iniciado sesión.
if(!LOG_IN)
	Core::Redirect('/dev/apps');

# Obtenemos la información de la aplicación a partir de su clave pública.
$app = Apps::GetPublic($P['public']);

# ¡La aplicación no existe!
if(!$app)
	Core::Redirect('/dev/apps');

$error = array();

# Nombre
if( empty($P['name']) )
	$error[] = 'El nombre de la aplicación no puede estar vacio.';

# Descripción
if( empty($P['description']) )
	$error[] = 'La descripción de la aplicación no puede estar vacia.';

# Sin errores.
if( empty($error) )
{
	# Actualizamos la información.
	Apps::Update(array(
		'name'			=> $P['name'],
		'description'	=> $P['description']
	), $app['id']);

	# Vamonos a la lista de nuestras aplicaciones.
	Core::Redirect('/dev/apps');
}
else
{
	# ¡Uy! Un error.
	$message = '';

	# Juntamos todos los errores en un solo mensaje separado por <li>
	foreach($error as $e)
		$message .= "<li>$e</li>";

	# Guardamos los errores en una sesión.
	_SESSION('app_error', $message);

	# Redireccionar a la página de antes.
	Core::Redirect('/dev/apps/' . $app['public_key']);
}
?>