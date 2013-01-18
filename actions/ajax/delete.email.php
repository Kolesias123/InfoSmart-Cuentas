<?
# Con esto hacemos que sea necesario iniciar sesión.
$page['require_login'] = true;
require '../../Init.php';

## --------------------------------------------------
## Eliminar correo electrónico alternativo.
## --------------------------------------------------
## Solo eso.
## --------------------------------------------------

$continue 	= false;
$result 	= array();

# Los correos alternativos no tienen ID, se tienen que eliminar a partir de la misma dirección.
# Por lo tanto si lo que se recibe no es un correo es porque: Algo malo sucedio en la página / Intento de hack.
if(!Core::Valid($P['email']))
	$error = '¡Vaya! Al parecer has roto algo, recarga la página y vuelve a intentarlo.';

# Verificamos que el correo aún este en la lista de correos alternativos.
else if(array_search($P['email'], $me_emails) === false)
{
	$continue 	= true;
	$error 		= 'Esta dirección de correo ya no se encuentra aliada a tu cuenta.';
}

# Sin errores.
if( empty($error) )
{
	# Eliminar el correo de la lista y actualizarla.
	$me_emails = array_delete($P['email'], $me_emails);
	$me_emails = json_encode($me_emails);

	# Guardar los cambios.
	Users::UpdateData('emails', $me_emails);

	$result['status'] = 'OK';
}
else
{
	# ¡Uy! Un error.
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
	$result['continue'] = ($continue) ? 'true' : 'false';
}

# Devolver el código JSON que será procesado por JavaScript.
echo json_encode($result);
?>