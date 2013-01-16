<?
$page['require_login'] 	= true;
$Kernel['gzip'] 		= false;
require '../../Init.php';

$result = array();
$upload = AcUsers::SaveFilePhoto($_FILES['photo']);

if($upload == INVALID)
	$error = '¡Uy! Al parecer no pudimos recibir tu foto de perfil, reinicia la página y vuelve a intentarlo.';

if($upload == TYPE_INVALID)
	$error = 'Solo puedes subir archivos de tipo PNG, JPEG o GIF';

if($upload == TOO_HEAVY)
	$error = 'La foto de perfil es muy grande, por favor sube una foto con un peso menor a 5 MB.';

if(empty($error))
	$result['status'] = 'OK';
else
{
	$result['status'] 	= 'ERROR';
	$result['message']	= htmlentities($error);
}

echo json_encode($result);
?>