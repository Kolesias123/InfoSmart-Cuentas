<?
$page['require_login'] = true;
require '../../Init.php';

$P['type'] = str_ireplace('privacy_', '', $P['type']);

if(!array_key_exists($P['type'], $PRIVACY))
	$error = '¡Vaya! Al parecer has roto algo, recarga la página y vuelve a intentarlo. 1';

if($P['value'] !== 'public' AND $P['value'] !== 'private')
	$error = '¡Vaya! Al parecer has roto algo, recarga la página y vuelve a intentarlo. 2';

if(empty($error))
{
	$type 				= $P['type'];

	$me_privacy[$type] 	= $P['value'];
	$me_privacy 		= json_encode($me_privacy);

	Users::UpdateData('privacy', $me_privacy);
	$result['status'] = 'OK';
}
else
{
	$result['status'] 	= 'ERROR';
	$result['message'] 	= htmlentities($error);
}

echo json_encode($result);
?>