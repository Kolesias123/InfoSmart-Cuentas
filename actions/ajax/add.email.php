<?
# Con esto hacemos que sea necesario iniciar sesión.
$page['require_login'] = true;
require '../../Init.php';

## --------------------------------------------------
## Agregar correo electrónico alternativo.
## --------------------------------------------------
## Solo eso.
## --------------------------------------------------

$result = array();

# Verificamos que el correo electronico sea válido.
if( empty($P['email']) )
	$error = 'Por favor escribe la dirección de correo.';

else if(!Core::Valid($P['email']))
	$error = 'Debes escribir una cuenta de correo electrónico válida.';

# También que no sea ya un correo alternativo.
else if(array_search($P['email'], $me_emails) !== false)
	$error = 'Esta dirección de correo ya se encuentra aliada a tu cuenta.';

# No puedes agregar tu propio correo primario como alternativo.
else if($P['email'] == $me['email'])
	$error = 'No puedes usar tu correo primario como una dirección de correo alternativa.';

# Este correo no debe ser el primario de ninguna otra cuenta.
else if(Users::Exist($P['email']))
	$error = 'Esta dirección de correo ya esta siendo ocupado por otra cuenta.';

# TODO: Verificar que no sea el correo alternativo de otra cuenta.

# Sin errores.
if( empty($error) )
{
	# Agregamos el correo alternativo a la lista y lo convertimos a JSON.
	$me_emails = array_merge($me_emails, (array)$P['email']);
	$me_emails = json_encode($me_emails);

	# Actualizamos la lista.
	Users::UpdateData('emails', $me_emails);
	$result['status'] = 'OK';
}
else
{
	# ¡Uy! Un error.
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
}

# Devolver el código JSON que será procesado por JavaScript.
echo json_encode($result);
?>