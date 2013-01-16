<?
if(!defined('BEATROCK'))
	exit;

class Apps
{

	## Crear una aplicación
	static function NewApp($name, $description, $ownerId = ME_ID)
	{
		Insert('apps', array(
			'name'			=> $name,
			'description'	=> $description,
			'ownerId'		=> $ownerId,
			'public_key'	=> Core::Random(30),
			'private_key'	=> Core::Random(60)
		));
	}

	## Obtener información de la aplicación con su llave pública.
	static function GetPublic($public)
	{
		q("SELECT * FROM {DA}apps WHERE public_key = '$public' LIMIT 1");
		return (num_rows() > 0) ? fetch_assoc() : false;
	}

	## Obtener información de la aplicación con su llave privada.
	static function GetPrivate($private)
	{
		q("SELECT * FROM {DA}apps WHERE private_key = '$private' LIMIT 1");
		return (num_rows() > 0) ? fetch_assoc() : false;
	}

	## ¿La aplicación ya tiene el permiso del usuario?
	static function Authorized($appId, $userId = ME_ID)
	{
		q("SELECT null FROM {DA}users_apps WHERE appId = '$appId' AND userId = '$userId' LIMIT 1");
		return (num_rows() > 0) ? true : false;
	}

	## Agregar permisos de uso a la aplicación.
	static function Authorize($appId, $userId = ME_ID)
	{
		# La aplicación ya tiene permisos, actualizar su "ultima vez usada"
		if(self::Authorized())
		{
			Update('users_apps', array(
				'last_used'	=> time()
			), array(
				"userId = '$userId' AND",
				"appId = '$appId'"
			));
		}

		# Agregar permisos...
		else
		{
			Insert('users_apps', array(
				'userId'	=> $userId,
				'appId'		=> $appId,
				'last_used' => time(),
				'date'		=> time()
			));
		}
	}

	## Actualizar información de una aplicación mediante un array.
	static function Update($data, $appId)
	{
		Update('apps', $data, array(
			"id = '$appId' OR",
			"public_key = '$appId'"
		));
	}

	## Actualizar columna de una aplicación.
	static function UpdateData($key, $value, $appId)
	{
		Update('apps', array(
			$key => $value
		), array(
			"id = '$appId' OR",
			"public_key = '$appId'"
		));
	}
}
?>