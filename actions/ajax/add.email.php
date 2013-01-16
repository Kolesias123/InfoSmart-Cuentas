<?
$page['require_login'] = true;
require '../../Init.php';

$result = array();

if(empty($P['email']))
	$error = 'Por favor escribe la dirección de correo.';

else if(!Core::Valid($P['email']))
	$error = 'Debes escribir una cuenta de correo electrónico válida.';

else if(array_search($P['email'], $me_emails) !== false)
	$error = 'Esta dirección de correo ya se encuentra aliada a tu cuenta.';

else if($P['email'] == $me['email'])
	$error = 'No puedes usar tu correo primario como una dirección de correo alternativa.';

else if(Users::Exist($P['email']))
	$error = 'Esta dirección de correo ya esta siendo ocupado por otra cuenta.';

if(empty($error))
{
	$me_emails = array_merge($me_emails, (array)$P['email']);
	$me_emails = json_encode($me_emails);

	Users::UpdateData('emails', $me_emails);
	$result['status'] = 'OK';
}
else
{
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
}

echo json_encode($result);
?>