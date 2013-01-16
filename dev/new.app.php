<?
require '../Init.php';

if(!LOG_IN)
	Core::Redirect('/connect/login?return=' . urlencode(PATH_NOW));

$error = _SESSION('new_app_error');

if( !empty($error) )
{
	Tpl::JavaAction('Kernel.ShowBox("error");');
	_DELSESSION('new_app_error');
}

$page['id'] 		= 'new.app';
$page['folder']		= 'dev';
$page['name']		= 'Nueva aplicación';
$page['dev']		= true;
?>