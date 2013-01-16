<?
require 'Init.php';

if(!LOG_IN)
	Core::Redirect('/connect/login');

$months 	= Date::GetListMonths();
$countrys 	= Site::Get(); 
$birth 		= explode('/', $me['birthday']);

$page['id'] 	= array('home', 'home.bar');
$page['home']	= true;
?>