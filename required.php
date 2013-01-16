<?
$page['id'] 	= 'required';
require 'Init.php';

if(!LOG_IN)
	Core::Redirect();

$errors = _SESSION('required_errors');
$data 	= _SESSION('required_data');

if(!empty($errors))
{
	Tpl::JavaAction('Kernel.ShowBox("error")');
	_DELSESSION('required_errors');
}

$months 	= Date::GetListMonths();
$countrys 	= Site::Get(); 

$page['name']	= 'Información requerida';
$page['home'] 	= true;
?>