<?
$Kernel['gzip'] 		= false;
$page['require_login'] 	= true;
require '../../Init.php';

$result = array();
$upload = AcUsers::VerifyPhotoAccess($_FILES['photo']);

if($upload == INVALID)
	$error = '¡Uy! Al parecer no pudimos recibir tu foto de acceso, reinicia la página y vuelve a intentarlo.';

if($upload == TYPE_INVALID)
	$error = 'Solo puedes subir archivos de tipo PNG, JPEG o GIF';

if($upload == TOO_HEAVY)
	$error = 'La foto de acceso es muy grande ¿Estás segur@ que es esta?';

if($upload == NO_EXIST)
	$error = 'No pudimos identificar ningún usuario con esta foto de acceso.';

if(empty($error))
{	
	Users::Login($upload, false);
	$result['status'] 	= 'OK';
}
else
{
	$result['status'] 	= 'ERROR';
	$result['message']	= '<li>' . htmlentities($error) . '</li>';
}

echo json_encode($result);
?>