<?
$page['logout'] = true;
require '../Init.php';

## --------------------------------------------------
## Cierre de sesión
## --------------------------------------------------
## Solo eso.
## --------------------------------------------------

# No hemos iniciado sesión, evitemos cualquier cosa innecesaria...
if(!LOG_IN)
	Core::Redirect();

# Cerrar sesión y actualizar datos.
Users::Logout();

# Redireccionar a la home (Que redireccionara al inicio de sesión)
Core::Redirect();
?>