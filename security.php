<?
require 'Init.php';

## --------------------------------------------------
## Home - Seguridad
## --------------------------------------------------
## Página para cambiar la seguridad de la cuenta
## del usuario, ya sabes, conexión segura (SSL), imagen
## de acceso, palabra mágica, etc...
## --------------------------------------------------

# Plantilla: home.profile.html
# Plantilla secundaria: home.bar.html (Barra de la derecha)
$page['id'] 	= array('home.security', 'home.bar.page');
# Nombre de la página: InfoSmart Cuentas - Seguridad
$page['name'] 	= 'Seguridad';
# Con esto tenemos los estilos y scripts apropiados.
$page['home'] 	= true;
?>