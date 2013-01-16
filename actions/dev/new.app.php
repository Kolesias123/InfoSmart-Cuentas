<?
require '../../Init.php';

if(!LOG_IN)
	Core::Redirect('/dev/apps');

$error = array();

if( empty($P['name']) )
	$error[] = 'El nombre de la aplicación no puede estar vacio.';

if( empty($P['description']) )
	$error[] = 'La descripción de la aplicación no puede estar vacia.';

if( empty($error) )
{
	Apps::NewApp($P['name'], $P['description']);
	Core::Redirect('/dev/apps');
}
else
{
	$message = '';

	foreach($error as $e)
		$message .= "<li>$e</li>";

	_SESSION('new_app_error', $message);
	Core::Redirect('/dev/apps/new');
}
?>