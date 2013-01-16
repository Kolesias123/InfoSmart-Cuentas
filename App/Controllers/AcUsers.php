<?
if(!defined('BEATROCK'))
	exit;

class AcUsers
{
	## Agregar un usuario, adaptado para InfoSmart Cuentas.
	static function NewUser($username, $password, $name, $email, $birthday = '', $photo = '', $auto = true, $params = '')
	{
		$userId = Users::NewUser($username, $password, $name, $email, $birthday, 'default', $auto, $params);

		mkdir(MEDIA . 'photos' . DS . $userId);

		if(is_array($photo))
		{
			if(!is_numeric($photo['size']))
				$photo['size'] 		= 80;
			
			if(empty($photo['rating']))
				$photo['rating'] 	= 'g';
				
			$photo = self::GetGravatar($email, '', $photo['size'], $photo['default'], $photo['rating']);
			$photo = file_get_contents($photo);

			self::SavePhoto($photo, $userId);
		}
	}

	#############################################################
	## FOTO DE PERFIL
	#############################################################

	## Guardar una foto de perfil a partir de los bits de la misma.
	## ¡OBSOLETA E INSEGURA!
	static function SavePhoto($data, $userId)
	{
		$path 	= Io::SaveTemporal($data, '.png');
		$mime 	= Io::Mimetype($path);
		$md5 	= md5_file($path);

		$file 	= MEDIA . 'photos' . DS . $userId . DS . $md5;

		if($mime !== 'image/png' AND $mime !== 'image/jpeg' AND $mime !== 'image/gif')
			return TYPE_INVALID;

		if(filesize($path) > 5242880)
			return TOO_HEAVY;

		@mkdir(MEDIA . 'photos' . DS . $userId);

		// Original
		Io::Write($file, $data);
		Users::UpdateData('photo', $md5, $userId);

		// 300x300
		Gd::Resize($path, $file . '.big', 300, 300);

		// 214x214
		Gd::Resize($path, $file . '.medium', 214, 214);

		// 124x124
		Gd::Resize($path, $file . '.small', 124, 124);

		return true;
	}

	## Guardar una foto de perfil a partir del $_FILE de la misma.
	static function SaveFilePhoto($photo, $userId = ME_ID)
	{
		# ¿No hay nada?
		if(empty($photo))
			return INVALID;

		# Las fotos son guardadas con su MD5 (Más practico)
		$md5 		= md5_file($photo['tmp_name']);
		# Ubicación final de la foto.
		$file 		= MEDIA . 'photos' . DS . $userId . DS . $md5;

		# Extensión de la foto.
		$ext 		= substr(strrchr($photo['name'], '.'), 1);
		$file_ext 	= $file . '.' . $ext;

		# Solo imagenes PNG, JPG y GIF
		if($photo['type'] !== 'image/png' AND $photo['type'] !== 'image/jpeg' AND $photo['type'] !== 'image/gif')
			return TYPE_INVALID;

		# Solo imagenes menores de 5 MB.
		if($photo['size'] > 5242880)
			return TOO_HEAVY;

		# Crear carpeta de imagenes para el usuario si no se ha creado.
		if(!is_dir(MEDIA . 'photos' . DS . $userId))
			mkdir(MEDIA . 'photos' . DS . $userId);

		# Original
		Io::Copy($photo['tmp_name'], $file_ext);
		Users::UpdateData('photo', $md5, $userId);
		Users::UpdateData('photo_ext', $ext, $userId);

		# 300x300
		Gd::Resize($file_ext, $file . '.big.' . $ext, 300, 300, false);

		# 214x214
		Gd::Resize($file_ext, $file . '.medium.' . $ext, 214, 214, false);

		# 124x124
		Gd::Resize($file_ext, $file . '.small.' . $ext, 124, 124, false);

		return OK;
	}

	## Guardar una foto de perfil a partir de una dirección web.
	## Usada para guardar el Gravatar del usuario.
	static function SaveUrlPhoto($photo, $userId = ME_ID)
	{
		# ¿No hay nada o es una dirección invalida?
		if(empty($photo) OR !Core::Valid($photo, 'url'))
			return INVALID;

		# Obtenemos los bits de la imagen.
		$data 		= file_get_contents($photo);
		# Lo guardamos en un archivo temporal que BeatRock eliminara al final.
		$path 		= Io::SaveTemporal($data, '.png');
		# Obtenemos el tipo de imagen que es (PNG, JPEG, GIF)
		$mime 		= Io::Mimetype($path);

		# Las fotos son guardadas con su MD5 (Más practico)
		$md5 		= md5_file($path);
		# Ubicación final de la foto.
		$file 		= MEDIA . 'photos' . DS . $userId . DS . $md5;

		# Extensión de la foto.
		$ext 		= 'png';
		$file_ext 	= $file . '.' . $ext;

		# Solo imagenes PNG, JPG y GIF
		if($mime !== 'image/png' AND $mime !== 'image/jpeg' AND $mime !== 'image/gif')
			return TYPE_INVALID;

		# Solo imagenes menores de 5 MB.
		if(filesize($path) > 5242880)
			return TOO_HEAVY;

		# Crear carpeta de imagenes para el usuario si no se ha creado.
		if(!is_dir(MEDIA . 'photos' . DS . $userId))
			mkdir(MEDIA . 'photos' . DS . $userId);

		# Original
		Io::Copy($path, $file_ext);
		Users::UpdateData('photo', $md5, $userId);
		Users::UpdateData('photo_ext', $ext, $userId);

		# 300x300
		Gd::Resize($file_ext, $file . '.big.' . $ext, 300, 300, false);

		# 214x214
		Gd::Resize($file_ext, $file . '.medium.' . $ext, 214, 214, false);

		# 124x124
		Gd::Resize($file_ext, $file . '.small.' . $ext, 124, 124, false);

		return OK;
	}

	## Obtener los bits de la foto de perfil de un usuario.
	## Usado para mostrar la foto en un archivo PHP y no directamente.
	static function GetPhoto($userId = ME_ID, $size = 'medium')
	{
		# ¿La foto predeterminada? ¡Claro!
		if($photo == 'default' OR $userId == 'default')
			return file_get_contents(RESOURCES_GLOBAL . '/images/id/photo.default.png');
		
		# El parametro $userId contiene nombre de usuario o email...
		# averiguar automaticamente la ID
		if(!is_numeric($userId))
			$userId = Users::Data('id', $userId);

		# Obteniendo el MD5 de la foto del usuario.
		$photo 	= Users::Data('photo', $userId);
		# Obteniendo la extensión de la foto del usuario.
		$ext 	= Users::Data('photo_ext', $userId);

		# Esta sería la ubicación de la foto.
		$file = MEDIA . 'photos' . DS . $userId . DS . $photo;

		# Esta pidiendo la foto pequeña.
		if($size == 'small')
			$file .= '.small';

		# Esta pidiendo la foto mediana.
		if($size == 'medium')
			$file .= '.medium';

		# Esta pidiendo la foto grande.
		if($size == 'big')
			$file .= '.big';

		# Sea cual sea, la extensión va al último.
		$file .= '.' . $ext;

		# ¡La foto no existe! Algo malo sucedio aquí...
		if(!file_exists($file))
			return NO_EXIST;

		# Regresar bits.
		return file_get_contents($file);
	}

	#############################################################
	## FOTO DE ACCESO
	#############################################################

	## Guardar una foto de acceso a partir del $_FILE de la misma.
	static function SaveFilePhotoAccess($photo, $userId = ME_ID)
	{
		# ¿No hay nada?
		if(empty($photo))
			return INVALID;

		# Las fotos son guardadas con su MD5 (Más practico)
		$md5 		= md5_file($photo['tmp_name']);
		# Ubicación final de la foto.
		$file 		= MEDIA . 'photos.access' . DS . $userId . DS . $md5;

		# Solo imagenes PNG, JPG y GIF
		if($photo['type'] !== 'image/png' AND $photo['type'] !== 'image/jpeg' AND $photo['type'] !== 'image/gif')
			return TYPE_INVALID;

		# Solo imagenes menores de 5 MB.
		if($photo['size'] > 5242880)
			return TOO_HEAVY;

		# Crear carpeta de imagenes para el usuario si no se ha creado.
		if(!is_dir(MEDIA . 'photos.access' . DS . $userId))
			mkdir(MEDIA . 'photos.access' . DS . $userId);

		# Guardar foto
		Io::Copy($photo['tmp_name'], $file);
		Users::UpdateData('photo_access', $md5, $userId);
		
		# Todo bien.
		return OK;
	}

	## Verificar foto de acceso a partir del $_FILE de la misma.
	static function VerifyPhotoAccess($photo)
	{
		# ¿No hay nada?
		if(empty($photo))
			return INVALID;

		# Obtener su MD5
		$md5 = md5_file($photo['tmp_name']);

		# Solo imagenes PNG, JPG y GIF
		if($photo['type'] !== 'image/png' AND $photo['type'] !== 'image/jpeg' AND $photo['type'] !== 'image/gif')
			return TYPE_INVALID;

		# Solo imagenes menores de 5 MB.
		if($photo['size'] > 5242880)
			return TOO_HEAVY;

		# Esta foto (MD5) no ha sido subida por ningún usuario.
		if(!Users::Exist($md5, 'photo_access'))
			return NO_EXIST;

		# Regresar la ID del usuario que subio esta curiosa foto.
		$userId = Users::DataOnly('id', $md5, 'photo_access'); 
	
		return $userId;
	}

	## Eliminar la foto de acceso.
	static function DeletePhotoAccess($userId = ME_ID)
	{
		# Obtener el MD5 de la foto.
		$md5 	= Users::DataOnly('photo_access', $userId);
		# Esta sería la ubicación de la foto.
		$file 	= MEDIA . 'photos.access' . DS . $userId . DS . $md5;

		# Eliminar
		Io::Delete($file);
		Users::UpdateData('photo_access', '', $userId);

		return true;
	}

	#############################################################
	## API
	#############################################################

	## Obtener usuario a partir de una clave de autorización.
	static function GetAuthorize($authorize, $appId)
	{
		# Consulta para obtener la información.
		q("SELECT * FROM {DA}users_apps_authorize WHERE authorize_key = '$authorize' LIMIT 1");
		
		# Esta clave de autorización no existe.
		# Nota: Las claves expiradas son eliminadas en 1 hora. (Nos falta dinero para un MySQL más grande...)
		if(num_rows() == 0)
			return AUTHKEY_NO_EXIST;

		# Obtenemos la información de la clave.
		$key = fetch_assoc();

		# ¡Expirada!
		if($key['expire'] <= time())
			return AUTHKEY_EXPIRED;

		# Un segundo... Esta clave no se ha asignado para esta aplicación. (¿Intento de Hacking?)
		if($key['appId'] !== $appId)
			return AUTHKEY_NO_APP_OWNER;

		# Retornar información del usuario.
		return Users::GetUser($key['userId']);
	}

	#############################################################
	## API - APLICACIONES
	#############################################################

	## Obtener las aplicaciones del usuario.
	static function GetApps($userId = ME_ID)
	{
		$sql = q("SELECT * FROM {DA}apps WHERE ownerId = '$userId' ORDER BY id DESC");
		return (num_rows() > 0) ? $sql : false;
	}
}
?>