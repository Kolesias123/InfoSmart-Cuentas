<?
require '../../Init.php';

## --------------------------------------------------
## Desarrolladores - Crear una aplicación
## --------------------------------------------------
## Crea una nueva aplicación.
## --------------------------------------------------

# No hemos iniciado sesión
if(!LOG_IN)
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
	# Crear nuva aplicación
	Apps::NewApp($P['name'], $P['description']);

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
	_SESSION('new_app_error', $message);

	# Redireccionar a la página de antes.
	Core::Redirect('/dev/apps/new');
}
?>