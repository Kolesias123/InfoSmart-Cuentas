<?
$page['api'] = true;
require '../../Init.php';

## --------------------------------------------------
##        			API: /main
## --------------------------------------------------
## Obtiene la ID del usuario y la aplicación.
## --------------------------------------------------

$result['userId'] 	= $data['user']['id'];
$result['app'] 		= $data['app'];

echo json_encode($result);
?>