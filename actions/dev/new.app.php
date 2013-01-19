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
	$features = array();

	## Pasamos cada uno de los campos de características y las ponemos en un array.
	## TODO: ¿Hay algo mejor que esto?
	foreach($P['feature_title'] as $key => $value)
	{
		# Titulo vacio, tomar como eliminación.
		if( empty($value) )
		{
			unset($P['feature_content'][$key], $P['feature_image'][$key]);
			continue;
		}

		$features[$key]['title'] = $value;
	}

	foreach($P['feature_content'] as $key => $value)
	{
		# Contenido vacio, tomar como eliminación.
		if( empty($value) )
		{
			unset($features[$key], $P['feature_image'][$key]);
			continue;
		}

		$features[$key]['content'] = $value;
	}

	$photos = array();

	## Al parecer $_FILES no me coopera, ahí que reordenar la forma en que me envia el array.
	foreach($_FILES['feature_image'] as $key => $value)
	{
		foreach($value as $vkey => $vvalue)
			$photos[$vkey][$key] = $vvalue;
	}

	# Guardar fotos
	if( !empty($photos) )
	{
		foreach($photos as $key => $file)
		{
			$md5 = Apps::SaveFilePhotoFeature($file);

			if(!is_string($md5))
			{
				$features[$key]['image'] = '';
				continue;
			}

			$features[$key]['image'] = $md5;
		}
	}

	# Crear nuva aplicación
	$appId = Apps::NewApp($P['name'], $P['description'], $features);

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