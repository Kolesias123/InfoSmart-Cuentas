<?
require '../Init.php';

Tpl::AllowCross('*');

$page['id'] 		= (LOG_IN) ? 'profile.bar' : 'profile.bar.default';


$page['folder'] 	= 'api';
$page['header'] 	= false;
$page['footer']		= false;
$page['subheader']	= false;
$page['subfooter']  = false;
?>