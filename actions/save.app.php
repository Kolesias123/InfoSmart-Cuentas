<?
require '../Init.php';

if(!LOG_IN)
	Core::Redirect('/dev/apps');

$app = Apps::GetPublic($P['public']);

if(!$app)
	Core::Redirect('/dev/apps');

$error = array();

if( empty($P['name']) )
	$error[] = 'El nombre de la aplicación no puede estar vacio.';

if( empty($P['description']) )
	$error[] = 'La descripción de la aplicación no puede estar vacia.';

if( empty($error) )
{
	Apps::Update(array(
		'name'			=> $P['name'],
		'description'	=> $P['description']
	), $app['id']);

	Core::Redirect('/dev/apps');
}
else
{
	$message = '';

	foreach($error as $e)
		$message .= "<li>$e</li>";

	_SESSION('app_error', $message);
	Core::Redirect('/dev/apps/' . $app['public_key']);
}
?>