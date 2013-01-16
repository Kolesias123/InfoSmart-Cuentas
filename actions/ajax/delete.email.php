<?
$page['require_login'] = true;
require '../../Init.php';

$continue 	= false;
$result 	= array();

if(!Core::Valid($P['email']))
	$error = '¡Vaya! Al parecer has roto algo, recarga la página y vuelve a intentarlo.';

else if(array_search($P['email'], $me_emails) === false)
{
	$continue 	= true;
	$error 		= 'Esta dirección de correo ya no se encuentra aliada a tu cuenta.';
}

if(empty($error))
{
	$me_emails = array_delete($P['email'], $me_emails);
	$me_emails = json_encode($me_emails);

	Users::UpdateData('emails', $me_emails);

	$result['status'] = 'OK';
}
else
{
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
	$result['continue'] = ($continue) ? 'true' : 'false';
}

echo json_encode($result);
?>