<?
require 'Init.php';

# El usuario solicitado no existe o queremos la foto "default"
if(!Users::UserExist($G['user']) OR $G['user'] == 'default')
{
	$data = AcUsers::GetPhoto('default');
	goto ShowPhoto;
}

# ¿De que tamaño quieres la imagen?
$size = $G['size'];

# Por predeterminado de tamaño medio.
if( empty($size) )
	$size = 'medium';

# Obtenemos los "bits" de la foto del usuario.
$data = AcUsers::GetPhoto($G['user'], $size);

ShowPhoto:
{
	# Permitir cargar desde cualquier dominio.
	Tpl::AllowCross('*');
	# Mostrar como imagen PNG
	Tpl::Image();

	# Lanzar "bits"
	echo $data;
}
?>