<?
require '../Init.php';

if(!LOG_IN)
	Core::Redirect('/connect/login?return=' . urlencode(PATH_NOW));

$apps = AcUsers::GetApps();

$page['id'] 		= 'apps';
$page['folder']		= 'dev';
$page['name']		= 'Aplicaciones';
$page['dev']		= true;
?>