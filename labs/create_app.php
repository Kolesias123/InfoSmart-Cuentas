<?
require '../Init.php';

$name 			= 'RedPxndx';
$description 	= 'Comunicate con las personas que también siguen los artistas que tu quieres.';

Insert('apps', array(
	'name'			=> $name,
	'description'	=> $description,
	'public_key'	=> Core::Random(30),
	'private_key'	=> Core::Random(60)
));

?>