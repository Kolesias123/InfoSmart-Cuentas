<?
require '../../Init.php';

if(LOG_IN)
	exit;

if(!Users::UserExist($P['id']))
	echo 'NO_EXIST';
else
	echo 'OK';
?>