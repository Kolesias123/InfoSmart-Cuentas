<?
require '../Init.php';

## ¡INCOMPLETO!

# Ya hemos iniciado sesión.
# Redireccionar a la página principal.
if(LOG_IN)
	Core::Redirect();

if(empty($P['password_id']) OR !Users::Exist($P['password_id']))
	Core::Redirect('/forgot?type=password');

if($P['password_where'] !== 'alt_email' AND $P['password_where'] !== 'social')
	Core::Redirect('/forgot?type=password');

$page['id']
?>