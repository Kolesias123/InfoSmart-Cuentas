<?
require '../../Init.php';

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

$app_features 	= json_decode($app['features'], true);
$error 			= array();

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

			if($md5 == false)
			{
				$features[$key]['image'] = ( empty($app_features[$key]['image']) ) ? '' : $app_features[$key]['image'];
				continue;
			}

			$features[$key]['image'] = $md5;
		}	
	}


	# Actualizamos la información.
	Apps::Update(array(
		'name'			=> $P['name'],
		'description'	=> $P['description'],
		'features'		=> json_encode($features)
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