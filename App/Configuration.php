<?
// Acción ilegal.
if(!defined('BEATROCK'))
	exit;

//######################################################
//	CONEXIÓN AL SERVIDOR SQL
//######################################################

$config['sql'] = array(

	'type' 		=> 'mysql',
	'host'		=> 'localhost',
	'user'		=> 'root',
	'pass'		=> 'XyoInfo7799@',
	'name'		=> 'accounts',
	'prefix'	=> '',
	'port'		=> '3306',

	'repair'		=> false,
	'repair.error'	=> true

);

//######################################################
//	UBICACIÓN DE LA APLICACIÓN
//######################################################

$config['site'] = array(

	'timezone'			=> 'America/Mexico_City',
	'path'				=> 'localhost/accounts',
	'admin'				=> 'localhost/beat/admin',
	'resources'			=> 'localhost/resources/accounts',
	'resources.global'	=> 'localhost/resources/systemv2'

);

//######################################################
//	SEGURIDAD
//######################################################

$config['security'] = array(

	'enabled'	=> false,
	'level'		=> 4,
	'hash'		=> 'tgJvE%u{x-2xk&lBl(&E{0*hy%B&(f{Jzkbhtij6uq(_wh}k)Lhom-Cl{)h/sz05D6{^xy){lC9c%at',
	'antiddos'	=> false

);

//######################################################
//	ERRORES	& LOGS
//######################################################

$config['errors'] = array(

	'details'		=> true,
	'hidden'		=> false,
	'email.reports'	=> 'kolesias17@gmail.com'

);

$config['logs'] = array(

	'capture'	=> false,
	'save'		=> 'onerror'

);

//######################################################
//	PARAMETROS EXTRA PARA EL SERVIDOR
//######################################################

$config['server'] = array(

	'gzip'		=> false,
	'host'		=> false,
	'timezone'	=> true,
	'ssl'		=> null,
	'backup'	=> true

);

//######################################################
//	MEMCACHE
//######################################################

$config['memcache'] = array(

	'host'	=> '',
	'port'	=> 11211

);
?>