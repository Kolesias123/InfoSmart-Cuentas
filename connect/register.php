<?
require '../Init.php';

$errors 	= _SESSION('register_errors');
$data 		= _SESSION('register_data');

if(!empty($errors))
{
	Tpl::JavaAction('Kernel.ShowBox("error")');
	_DELSESSION('register_errors');
}

$months 	= Date::GetListMonths();
$countrys 	= Site::Get(); 

$page['id']	 		= 'register';
$page['folder']		= 'connect';
$page['name']		= 'Registro';
$page['subheader'] 	= 'login.header';
$page['login']		= true;
?>