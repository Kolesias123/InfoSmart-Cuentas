<?
require '../Init.php';

if(LOG_IN)
	Core::Redirect();

$allowed = array('facebook', 'twitter', 'steam');

if(!in_array($G['type'], $allowed))
	Core::Redirect();

$info 	= Social::Get($G['type']);
$verify = Users::ServiceExist($info['id'], $G['type']);

if(!$verify)
{
	$username = $info['username'];

	if(Users::Exist($info['username'], 'username'))
		$username = $G['type'] . '_' . $username;

	$hash 	= Users::NewService($info['id'], $G['type'], $info['username'], json_encode($info));
	$userId = Users::NewUser($username, '', $info['name'], $info['email'], $info['birthday'], '', true, array(
		'emails'		=> '[]',
		'country'		=> $info['country'],
		'gender'		=> $info['gender'],
		'privacy'		=> '{"email":"private","birthday":"private","lastaccess":"public","os":"private","country":"private"}',
		'service_hash'	=> $hash
	));

	if(Core::Valid($info['profile_image_url'], 'url'))
		AcUsers::SaveUrlPhoto($info['profile_image_url'], $userId);
}
else
{
	Social::Login($G['type'], $info);	
}

Core::Redirect();
?>