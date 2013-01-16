<?
require 'Init.php';

## --------------------------------------------------
## Home
## --------------------------------------------------
## Esta página es solicitada por AJAX cuando el usuario
## cambia su información.
## --------------------------------------------------

# No hemos iniciado sesión.
if(!LOG_IN)
	Core::Redirect('/connect/login');

# Obtener una lista de meses en el idioma del usuario.
$months 	= Date::GetListMonths();
# Obtener una lista de los países.
$countrys 	= Site::Get(); 
# Separar la fecha de nacimiento en día, mes y año.
$birth 		= explode('/', $me['birthday']);

# Plantilla: home.html
# Plantilla secundaria: home.bar.html (Barra de la derecha)
$page['id'] 	= array('home', 'home.bar');
# Con esto tenemos los estilos y scrips apropiados.
$page['home']	= true;
?>