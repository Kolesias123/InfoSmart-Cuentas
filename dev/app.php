<?
require '../Init.php';

if(!LOG_IN)
	Core::Redirect('/connect/login?return=' . urlencode(PATH_NOW));

$app = Apps::GetPublic($G['public']);

if(!$app)
	Core::Redirect('/dev/apps');

foreach($app as $key => $value)
	_t('app_' . $key, $value);

$features 	= json_decode($app['features'], true);
$error 		= _SESSION('app_error');

if( !empty($error) )
{
	Tpl::JavaAction('Kernel.ShowBox("error");');
	_DELSESSION('app_error');
}

$page['id'] 	= 'app';
$page['folder']	= 'dev';
$page['dev']	= true;
?>