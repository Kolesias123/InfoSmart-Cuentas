<?
#####################################################
## 					 BeatRock
#####################################################
## Framework avanzado de procesamiento para PHP.
#####################################################
## InfoSmart © 2012 Todos los derechos reservados.
## http://www.infosmart.mx/
#####################################################
## http://beatrock.infosmart.mx/
#####################################################

// Acción ilegal.
if(!defined('BEATROCK'))
	exit;
	
class Codes
{
	static $codes = array();

	// Inicializar el sistema de códigos de errores.
	function __construct()
	{
		$folder = LANGUAGES . LANG;

		if(!is_dir($folder))
			$folder = LANGUAGES . 'es';

		if(!is_dir($folder))
			$folder = LANGUAGES . 'en';

		if(!is_dir($folder))
			exit('ERROR - Codes - No se ha podido encontrar la carpeta de lenguajes en: ' . LANGUAGES);

		$data = Core::LoadJSON($folder . DS . 'Codes.json');

		foreach($data as $key => $value)
		{
			$nkey 			= self::HexCode($key);
			$data[$nkey]	= $value;

			unset($data[$key]);
		}

		self::$codes = $data;
	}

	// Transformar cadena de texto a código hexadecimal.
	// - $str: Cadena de texto.
	static function HexCode($str)
	{
		$result = '';

		for($i = 0; isset($str[$i]); ++$i)
		{
			$char 	= dechex(ord($str[$i]));
			$result .= $char;
		}

		return '0x' . strtoupper($result);
	}
	
	// Obtener la información de un código de error.
	// - $code: Código de error.
	static function GetInfo($code)
	{
		$code   		= self::HexCode($code);
		$result 		= _C(self::$codes[$code]);
		
		if(empty($result['title']))
			$result = _C(self::$codes['0x756E6B6E6F77']);

		$result['code'] = $code;
		return $result;
	}
}
?>