<?
$page['api'] = true;
require '../../Init.php';

## --------------------------------------------------
##        			API: /me
## --------------------------------------------------
## Obtiene la información del usuario.
## Aplicando la información solicitada de la app.
## --------------------------------------------------

$user = API::FilterInfo($data['user']);
echo json_encode($user);
?>