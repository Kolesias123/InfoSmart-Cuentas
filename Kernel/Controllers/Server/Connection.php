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

class Connection
{
	// Socket de la conexi�n.
	public $socket = null;
	// �ltima actividad.
	public $last 	= 0;
	// ID de la conexi�n.
	public $id 	= 0;
	
	// Funci�n - Preparar una nueva conexi�n.
	function __construct($socket, $id)
	{
		$this->socket 	= $socket;
		$this->id 		= $id;
		$this->last 	= time();
	}

	// Funci�n privada - �Hay alguna conexi�n activa?
	function Ready()
	{
		if($this->socket == null OR !is_resource($this->socket))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	function Kill()
	{
		if(!$this->Ready())
			return;

		socket_shutdown($this->socket);
		socket_close($this->socket);
		
		Server::Write('CONEXI�N #' . $this->id . ' CERRADA.');
		$this->socket = null;
	}

	// Funci�n - Liberar conexi�n activa.
	function Clean()
	{
		if(!$this->Ready())
			return;
		
		socket_close($this->socket);
	}
	
	// Funci�n - Enviar datos.
	// - $data: Datos a enviar.
	// - $response (Bool): �Queremos esperar una respuesta?
	function Send($data, $response = false)
	{
		if(!$this->Ready())
			return false;
			
		$len = strlen($data);
		$off = 0;		
		
		while($off < $len)
		{
			$send = socket_write($this->socket, substr($data, $off), $len - $off);

			if(!$send)
				break;
			
			$off += $send;
		}
		
		if($off < $len)
		{
			Server::Write('Ha ocurrido un error al intentar enviar los datos '.$data.'.');
			return false;
		}			

		Server::Write("Se ha enviado '$data' a la conexi�n #" . $this->id . ".");
			
		if(!$response)
		{
			$this->Clean();
			return true;
		}
		
		$bytes = @socket_recv($this->socket, $data, 2048, 0);
		$this->Clean();

		return $data;
	}
}
?>