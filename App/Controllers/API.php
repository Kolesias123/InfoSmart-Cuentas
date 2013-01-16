<?
if(!defined('BEATROCK'))
	exit;

class API
{
	# Campos a ser excluidos de la información del usuario.
	static $PRIVATE = array(
		'password',
		'email_verified',
		'photo',
		'photo_ext',
		'photo_access',
		'profile',
		'rank',
		'reg_ip',
		'newsletter',
		'cookie',
		'service_hash',
		'secret',
		'banned',
		'service',
		'ext_api_session',
		'secure',
		'magic_word'
	);

	/** LLAVES DE AUTORIZACIÓN **/

	## Crear llave de autorización.
	## Predeterminadamente dura 1 hora.
	static function NewAuthorizeKey($appId, $userId = ME_ID, $expire = 1)
	{
		$key = Core::Random(50);
		$exp = time() + ($expire * 60 * 60);

		Insert('users_apps_authorize', array(
			'userId'		=> $userId,
			'appId'			=> $appId,
			'authorize_key'	=> $key,
			'date'			=> time(),
			'expire'		=> $exp
		));

		return $key;
	}

	/** AUTENTICACIÓN DE API **/

	## Verificar el acceso a la API.
	## O en si, verifica que la llave privada y llave de autorización sean correctos.
	static function VerifyAPIAccess($private, $authorize)
	{
		$app = Apps::GetPrivate($private);

		if($app == false)
			return APP_NO_EXIST;

		$user = AcUsers::GetAuthorize($authorize, $app['id']);

		if(!is_array($user))
			return $user;

		return true;
	}

	## Verificar el acceso a la API.
	## Lo mismo de arriba pero retornando la información del usuario y la aplicación.
	static function GetAPIAccess($private, $authorize)
	{
		$app = Apps::GetPrivate($private);

		if($app == false)
			return APP_NO_EXIST;

		$user = AcUsers::GetAuthorize($authorize, $app['id']);

		if(!is_array($user))
			return $user;

		$result['user'] = $user;
		$result['app']	= $app;

		return $result;
	}

	/** ERRORES **/

	## Obtener el titulo y descripción de un error.
	static function GetCodeError($code)
	{
		q("SELECT * FROM {DA}api_errors WHERE code = '$code' LIMIT 1");
		return (num_rows() > 0) ? fetch_assoc() : false;
	}

	## Mostrar código JSON de un error.
	static function Error($result)
	{
		if($result === true OR is_array($result))
			return;

		$error = self::GetCodeError(strval($result));

		$return = array(
			'error' => array(
				'code'			=> $error['code'],
				'title'			=> htmlentities($error['title']),
				'description'	=> htmlentities($error['description'])
			)
		);

		echo json_encode($return);
		exit;
	}

	/** USUARIOS y FILTROS **/

	## Filtro para eliminar información privada del usuario.
	## Usa la variable $PRIVATE de la clase.
	static function FilterInfo($data)
	{
		foreach(self::$PRIVATE as $key)
			unset($data[$key]);

		return $data;
	}
}
?>