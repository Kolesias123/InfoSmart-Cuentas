<?
#####################################################
## 					 BeatRock				   	   ##
#####################################################
## Framework avanzado de procesamiento para PHP.   ##
#####################################################
## InfoSmart © 2012 Todos los derechos reservados. ##
## http://www.infosmart.mx/						   ##
#####################################################
## http://beatrock.infosmart.mx/				   ##
#####################################################

// Acción ilegal.
if(!defined('BEATROCK'))
	exit;

## --------------------------------------------------
##        Funciones externas y globales
## --------------------------------------------------
## Utilice este archivo para definir funciones
## independientes del Kernel, también
## para definir procesos que se deban repetir
## en toda la aplicación.
## --------------------------------------------------

#####################################################
## ADMINISTRACIÓN	
#####################################################

// Si estamos o queremos visitar la administración.
/*
if($page['admin'])
{
	
		¡No olvide descomentar esta línea!

	if(!LOG_IN OR $my['rank'] < 7)
		Core::Redirect();
	
	$page['folder'] 	= 'admin';
	$page['subheader'] 	= 'Admin.SubHeader';
	$page['subfooter'] 	= 'Admin.SubFooter';
	$page['compress'] 	= false;
}
*/

#############################################################
## ¡EN MANTENIMIENTO!
#############################################################

if($site['site_status'] !== 'open' AND $page['maintenance'] !== false)
{
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: no-cache');

	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	
	exit(Tpl::Process(KERNEL_TPL_BIT . 'Maintenance'));
}

#############################################################
## CONFIGURACIÓN EXTRA
#############################################################

/*
	BBCode -> Core::BBCode(string, smilies)
	Desde aquí puedes editar los códigos BB.
*/

$kernel['bbcode_search'] = array(
	'/\[b\](.*?)\[\/b\]/is', 
	'/\[i\](.*?)\[\/i\]/is', 
	'/\[u\](.*?)\[\/u\]/is', 
	'/\[s\](.*?)\[\/s\]/is', 
	'/\[url\=(.*?)\](.*?)\[\/url\]/is', 
	'/\[color\=(.*?)\](.*?)\[\/color\]/is', 
	'/\[size=small\](.*?)\[\/size\]/is', 
	'/\[size=large\](.*?)\[\/size\]/is', 
	'/\[size\=(.*?)\](.*?)\[\/size\]/is', 
	'/\[code\](.*?)\[\/code\]/is',
		
	'/\[youtube\=(.*?)x(.*?)\](.*?)\[\/youtube\]/is',
	'/\[vimeo\=(.*?)x(.*?)\](.*?)\[\/vimeo\]/is'
);
		
$kernel['bbcode_replace'] = array(
	'<strong>$1</strong>', 
	'<i>$1</i>', 
	'<u>$1</u>', 
	'<s>$1</s>', 
	'<a href="$1">$2</a>', 
	'<label style="color: $1;">$2</label>', 
	'<label style="font-size: 9px;">$1</label>', 
	'<label style="font-size: 14px;">$1</label>', 
	'<label style="font-size: $1px;">$2</label>', 
	'<pre>$1</pre>',
			
	'<iframe title="YouTube" width="$1" height="$2" src="https://www.youtube.com/embed/$3" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
	'<iframe title="Vimeo" width="$1" height="$2" src="http://player.vimeo.com/video/$3?badge=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'
);

/*
	Smilies -> Core::Smilies(string, bbcode)
	Desde aquí puedes editar las imagenes usadas para los emoticones.
	Las imagenes se encuentran en: /Kernel/BitRock/Emoticons/ (Obligatorio ser PNG)
*/

$kernel['emoticons'] = array(
	':D' 	=> 'awesomes',
	':)' 	=> 'happy',
	'D:' 	=> 'ohnoes',
	':0' 	=> 'ohnoes',
	':O' 	=> 'ohnoes',
	'OMG' 	=> 'ohnoes',
	':3' 	=> 'meow',
	'.___.' => 'huh',
	':S' 	=> 'confused',
	':P' 	=> 'lick',
	'^^' 	=> 'laugh',
	':(' 	=> 'sad',
	';)' 	=> 'wink',
	':B' 	=> 'toofis',
	'jelly' => 'jelly',
	'jalea' => 'jelly'
);


#####################################################
## FUNCIONES GLOBALES
#####################################################

#####################################################
## DEFINICIONES GLOBALES
#####################################################

$PRIVACY = array(
	'email' 		=> 'Correo electrónico',
	'gender'		=> 'Sexo',
	'birthday'		=> 'Fecha de nacimiento',
	'lastaccess'	=> 'Último acceso',
	'browser'		=> 'Navegador web',
	'os'			=> 'Sistema operativo',
	'country'		=> 'Ubicación'
);

Social::Prepare(array(
	'facebook' 	=> array(
		'appId'		=> '246543258705691',
		'secret'	=> '24c08e56bdd2220fd6713e36c022ddab'
	),

	'twitter' 	=> array(
		'key'		=> '3SU15Ig8SQeVeo1qbGxe8A',
		'secret'	=> 'SufPE4yV5kXw9jBfrxn04OQs488OUjK9ZjaiUmjps'
	),

	'vt'		=> array(
		'apiKey'	=> '3471758cec85822c030b2302ce5c808a9ca86b85ba15ebb3e3c0e09f7ee8b049'
	)
));

//Fb::$redirect 	= PATH . '/actions/login.social.php?type=facebook';
Fb::$scope 		= array('email', 'user_about_me', 'user_birthday', 'user_hometown', 'user_location', 'user_status', 'user_website');

define('MEDIA', $site['media_path']);

#####################################################
## INFOSMART CUENTAS
#####################################################

if(LOG_IN)
{
	$me_emails 	= json_decode($me['emails'], true);
	$me_privacy = json_decode($me['privacy'], true);

	/*if($me['secure'] == '1' AND SSL !== 'on' AND !Contains(PATH, 'localhost'))
	{
		Client::SavePost();				
		Core::Redirect('https://' . URL);
	}*/

	if($page['id'] !== 'required' AND $page['logout'] !== true)
	{
		$fields_required = array('email', 'firstname', 'lastname', 'name', 'gender', 'country');

		foreach($fields_required as $field)
		{
			if(empty($me[$field]))
				Core::Redirect('/required');
		}
	}
}

#####################################################
## INFOSMART CUENTAS - API
#####################################################

if($page['api'] == true)
{
	$data = API::GetAPIAccess($R['private'], $R['authorize']);
	API::Error($data);
}
?>