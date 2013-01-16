<?
#####################################################
## 					 BeatRock				   	   ##
#####################################################
## Framework avanzado de procesamiento para PHP.   ##
#####################################################
## InfoSmart � 2012 Todos los derechos reservados. ##
## http://www.infosmart.mx/						   ##
#####################################################
## http://beatrock.infosmart.mx/				   ##
#####################################################

// Acci�n ilegal.
if(!defined('BEATROCK'))
	exit;

class Server
{
	// Socket del servidor.
	static $socket 			= null;
	// Array con los recursos "Connection" de las conexiones entrantes.
	static $rsock 			= array();
	// Array de los recursos de escucha de las conexiones entrantes.
	static $asock 			= array();

	// Numero de conexiones entrantes online.
	static $count 			= 0;
	// Numero de conexiones recibidas.
	static $conn_count 		= 0;

	// �ltimo chequeo de Ping.
	static $last_ping_time 	= 0;
	static $ping_time 		= 3;
	// �ltima vez que se imprimieron las estadisticas.
	static $last_stats_time = 0;
	static $stats_time 		= 10;
	// Tiempo m�ximo de inactividad de la conexi�n entrante.
	static $conn_timeout 	= 5;

	// Numero de conexiones entrantes m�ximas.
	static $max_connections = 100;
	// Numero de conexiones online m�ximas.
	static $max_online 		= 5;

	static $actions = array();

	static function GetCount()
	{
		return self::$count;
	}

	static function GetConnCount()
	{
		return self::$conn_count;
	}

	static function SetConnTimeout($timeout = 5)
	{
		self::$conn_timeout = $timeout;
	}

	static function SetMaxConnections($max = 100)
	{
		self::$max_connections = $max;
	}

	static function SetMaxOnline($max = 5)
	{
		self::$max_online = $max;
	}

	static function SetPingTime($time)
	{
		self::$ping_time = $time;
	}

	// Funci�n - Lanzar error.
	// - $code: C�digo de error.
	// - $function: Funci�n causante.
	// - $message: Mensaje del error.
	static function Error($code, $function, $message = '')
	{		
		if(empty($message) AND is_resource(self::$socket))
			$message = socket_strerror(socket_last_error(self::$socket));
		
		BitRock::SetStatus($message, __FILE__, array('function' => $function));
		BitRock::LaunchError($code);
		
		return false;
	}
	
	// Funci�n - �Hay alguna conexi�n activa?
	static function Ready()
	{
		if(self::$socket == null OR !is_resource(self::$socket))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	static function Kill()
	{
		if(!self::Ready())
			return;
		
		socket_shutdown(self::$socket);
		socket_close(self::$socket);
		
		BitRock::log('Se ha apagado el servidor correctamente.');
		self::Write('SERVIDOR APAGADO CORRECTAMENTE');
		
		self::$socket 			= null;
		self::$rsock 			= array();
		self::$asoc 			= array();

		self::$count 			= 0;
		self::$connections 		= 0;

		self::$last_ping_time 	= 0;
		self::$last_stats_time 	= 0;
	}
	
	// Funci�n - Imprimir un log.
	// - $mesage: Mensaje.
	static function Write($message)
	{
		// Convertir el mensaje a ISO-8859-1
		$message = iconv('ISO-8859-1', "ASCII//TRANSLIT//IGNORE", $message);
		// Imprimir el mensaje.
		echo "[" . date('H:i:s') . "] - $message", PHP_EOL;
		@ob_flush();
	}

	static function BreakLine($line = false)
	{
		if($line == true)
			echo '--------------------------------------------------------------', PHP_EOL;
		else
			echo PHP_EOL;

		@ob_flush();
	}
	
	// Funci�n - Preparar un servidor interno.
	// - $local: Direcci�n para la conexi�n.
	// - $port (Int): Puerto de escucha.
	// - $timeout (Int): Tiempo de ejecuci�n limite.
	// - $ctimeout (Int): Tiempo de inactividad limite para las conexiones entrantes.
	function __construct($local = '127.0.0.1', $port = 1212, $ctimeout = 5, $timeout = 0)
	{
		set_time_limit($timeout);
		ob_implicit_flush(1);
		
		self::Kill();	
		self::Write('Preparando servidor...');
		
		// Crear el Socket para el servidor.
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or self::Error('01s', __FUNCTION__);		
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($socket, $local, $port) or self::Error('01s', __FUNCTION__);
		
		self::Write('Conexi�n creada.');
		
		// Crear el Socket encargado de escuchar las nuevas conexiones.
		socket_listen($socket) or self::Error('02s', __FUNCTION__);
		
		// Estableciendo el Socket del servidor.
		self::$socket 	= $socket;
		// Estableciendo en el array el socket de escucha.
		self::$asock 	= array($socket);

		// Estamos en un servidor, esto evitar� que se cargue el sistema de plantillas.
		Socket::$server = true;
		
		self::Write('Escuchando conexiones entrantes desde el puerto: ' . $port);
		self::Write('SERVIDOR INICIADO.');

		if(!is_numeric($ctimeout) OR $ctimeout < 0)
			$ctimeout = 5;
	
		// Estableciendo el tiempo m�ximo de inactividad.		
		self::$conn_timeout = $ctimeout;
		// Estableciendo la �ltima vez que se hizo chequeo de Ping.
		self::$last_ping_time 	= time();
		// Estableciendo la �ltima vez que se imprimieron las estadisticas.
		self::$last_stats_time 	= time();
		
		// Empezar a escuchar conexiones.
		self::Process();
	}
	
	// Funci�n - Recibir conexiones.
	static function Process()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
		
		// Bucle infinito para escuchar conexiones.
		while(true)
			self::Check();
	}
	
	// Funci�n - Checar sockets y conexiones.
	static function Check()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);

		// Antes de todo chechar las estadisticas y el Ping.
		self::Stats();

		$w = $r = NULL;
		
		$asock = self::$asock;
		// �Han habido cambios en los Sockets?
		$updates = socket_select($asock, $w, $r, 0);

		// No a�n no...
		if($updates == false)
			return;
		
		foreach($asock as $conn)
		{			
			// Si el Socket a revisar es el Socket que escucha nuevas conexiones...
			if($conn == self::$socket)
				self::NewConnection();
			// Si el Socket a revisar es un Socket de usuario.
			else
				self::NewData($conn);
		}
	}

	static function NewConnection()
	{
		// LIMITE DE CONEXIONES ALCANZADA.
		if(self::$conn_count > self::$max_connections AND self::$max_connections !== 0)
		{
			// AQU� PUEDES ENVIAR UN PAQUETE DE RESPUESTA INDICANDO QUE EL SERVIDOR YA NO ESTA DISPONIBLE.
			return false;
		}

		// �Alguien se quiere conectar a nuestro servidor?
		$incoming = socket_accept(self::$socket);
				
		// Hasta ahora nadie...
		if($incoming < 0)
			return false;

		// LIMITE DE USUARIOS ONLINE ALCANZADO
		if(self::$count > self::$max_online AND self::$max_online !== 0)
		{
			self::Write('SERVIDOR LLENO');
			// AQU� PUEDES ENVIAR UN PAQUETE DE RESPUESTA INDICANDO QUE EL SERVIDOR ESTA LLENO.
			return false;
		}

		// Inicializando instancia "Connection" para la nueva conexi�n.
		$newconn = new Connection($incoming, self::$conn_count);

		// Guardando esta nueva instancia en $rsock
		self::$rsock[self::$conn_count] = $newconn;
		// Guarando el Socket de esta conexi�n en $asock
		array_push(self::$asock, $incoming);
						
		// Uno m�s en nuestro servidor ;)
		self::BreakLine(true);
		self::Write('NUEVA CONEXI�N ENTRANTE #' . self::$conn_count);
		++self::$conn_count;

		if(self::$conn_count > self::$max_connections AND self::$max_connections !== 0)
			self::Write('LIMITE DE CONEXIONES ALCANZADO, NO SE ACEPTAR�N M�S CONEXIONES.');
	}

	static function NewData($conn)
	{
		// Revisar si se ha recibido informaci�n del Socket. (Paquetes)
		$bytes = @socket_recv($conn, $data, 2048, 0);
				
		// Al parecer no... �Siguiente!
		if(empty($data) OR !is_numeric($bytes))
			continue;
				
		// Obtener la ID del Socket.
		$i = array_search($conn, self::$asock);
				
		// Mmm... �Se le fue el Internet?
		if($i == false)
			continue;
				
		// FIXME - Un ajuste de ID.
		$i 			= $i - 1;
		// Obtener lista de acciones.
		$actions 	= self::$actions;
		// Obtener el recurso "Connection" de este Socket.
		$connection = self::$rsock[$i];
				
		self::Write('RECIBIENDO DATOS ('.$data.')');
				
		// Al parecer hay una acci�n para esta informaci�n/paquete.
		if(isset($actions[$data]))
		{
			// Ejecutar acci�n.
			$callback = $actions[$data];
			$callback($connection);
		}
					
		// Al parecer hay una acci�n a ejecutar al recibir cualquier informaci�n/paquete.
		if(isset($actions['*']))
		{
			// Ejecutar acci�n.
			$callback = $actions['*'];
			$callback($connection, $data);
		}
				
		// �ltima actividad de la conexi�n/usuario.
		$connection->last = time();
	}
	
	// Funci�n - Chequeo de conexiones.
	static function Stats()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);

		$count = count(self::$rsock);

		// Checar Ping y obtener usuarios online.
		if(self::$last_ping_time <= (time() - self::$ping_time))
		{
			$count = self::Ping();
			// Estableciendo la �ltima vez que se hizo chequeo de Ping.
			self::$last_ping_time 	= time();
		}
			
		// Imprimir las estadisticas.
		if(self::$last_stats_time <= (time() - self::$stats_time))
		{
			self::Write($count . ' conexiones activas - '.self::$conn_count.' conexiones entrantes - ' . round(memory_get_usage() / 1024,1) . ' KB de Memoria.');
			// Estableciendo la �ltima vez que se imprimieron las estadisticas.
			self::$last_stats_time 	= time();
		}
		
		// Estableciendo usuarios online.
		self::$count 			= $count;
	}
	
	// Funci�n - Ping de conexiones.
	static function Ping()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
			
		// Verificando conexi�n por conexi�n.
		foreach(self::$rsock as $conn)
		{
			$connection = $conn->socket;
			
			// La conexi�n se ha desconectado pac�ficamente, quitarla de la lista.
			if($connection == null)
			{
				self::Write('DESCONEXI�N #' . $conn->id);
				unset(self::$rsock[$conn->id]);
				
				continue;
			}
			
			// La conexi�n ha pasado el tiempo de inactividad, quitarla de la lista.
			if($conn->last <= (time() - (self::$conn_timeout)))
			{
				$conn->Kill();				
				unset(self::$rsock[$conn->id]);

				self::Write('CONEXI�N CERRADA #' . $conn->id);
				
				continue;
			}
		}
		
		// Devolver usuarios online.
		return count(self::$rsock);
	}
	
	// Funci�n - Enviar datos a todas las conexiones.
	static function SendAll($data)
	{		
		foreach(self::$rsock as $conn)
			$conn->Send($data, false);
	}

	// Funci�n - Agregar una acci�n.
	// - $data: Dato a recibir para activar acci�n.
	// - $action: Funci�n a realizar.
	static function AddAction($data, $action = '')
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
				self::$actions[$key] = $value;
		}
		else
			self::$actions[$data] = $action;
	}
}
?>