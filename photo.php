<?
require 'Init.php';

if(!Users::UserExist($G['user']) OR $G['user'] == 'default')
{
	$data = AcUsers::GetPhoto('default');
	goto ShowPhoto;
}

$size = $G['size'];

if(empty($size))
	$size = 'medium';

$data = AcUsers::GetPhoto($G['user'], $size);

ShowPhoto:
{
	Tpl::AllowCross('*');
	Tpl::Image();

	echo $data;
}
?>