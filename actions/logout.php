<?
$page['logout'] = true;
require '../Init.php';

if(!LOG_IN)
	Core::Redirect();

Users::Logout();

Core::Redirect();
?>