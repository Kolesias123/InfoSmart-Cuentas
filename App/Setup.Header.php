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
##        Funciones de cabecera
## --------------------------------------------------
## Utilice este archivo para definir la 
## implementación de recursos CSS/JS/Meta en
## su aplicación, utilice la variable $page[id]
## para separar recursos de páginas únicas.
## --------------------------------------------------
	
#####################################################
## IMPLEMENTACIÓN DE RECURSOS RECOMENDADOS.
#####################################################

// Implementando jQuery.
Tpl::jQuery();

// Implementando el estilo predeterminado y el Kernel en JavaScript.
Tpl::Style('style', true);
Tpl::Script('functions.kernel', true);

Tpl::Script('functions.base', true);
Tpl::Script('functions.alerts', true);
Tpl::Script('functions.tips', true);

Tpl::Script('php.min', true, true);
Tpl::Script('html5slider.js', true, true);

//Tpl::AddScript('//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit');

// Si queremos RSS...
if($site['site_rss'] == 'true')
	Tpl::Stuff('<link rel="alternate" type="application/rss+xml" title="%site_name%: RSS" href="'.Short($site['site_rss_path']).'" />');

#####################################################
## AGREGANDO ESTILOS SEGÚN PÁGINA
#####################################################

if($page['login'] == true)
{
	Tpl::Style('style.login');
	Tpl::Script('functions.login');
}

if($page['home'] == true)
{
	Tpl::Style('style.home');
	Tpl::Script('functions.home');
}

if($page['id'] == 'forgot')
{
	Tpl::Style('style.login');
	Tpl::Script('functions.forgot');
}

if($page['dev'] == true)
{
	Tpl::Style('style.dev');
	Tpl::Script('functions.dev');
}

Tpl::Style('style.forms');

if(LOG_IN)
{
	Tpl::AddVar('My_Username', $me['username']);
}

#####################################################
## INICIALIZACIÓN
#####################################################

if(empty($page['image']) AND defined('LOGO'))
	$page['image'] = LOGO;

if(!empty($page['image']))
{
	Tpl::Meta('og:image', 	$page['image'], 'property');
	Tpl::Meta('image', 		$page['image'], 'itemprop');
}

$HLANG = Lang::PrepareLive();
$HLANG = json_encode(_c($HLANG));

Tpl::Meta('name', $site['site_name'], 'itemprop');
Tpl::Meta('description', $site['site_description'], 'itemprop');

#####################################################
## OPEN GRAPH
#####################################################

Tpl::Meta('og:title', 		$site['name'], 'property');
Tpl::Meta('og:site_name', 	SITE_NAME, 'property');
Tpl::Meta('og:url', 		PATH_NOW, 'property');
Tpl::Meta('og:description', $site['site_description'], 'property');
Tpl::Meta('og:type', 		$site['site_type'], 'property');

if(!empty($site['site_locale']))
	Tpl::Meta('og:locale', $site['site_locale'], 'property');

if(!empty($site['site_og']))
{
	$og = json_decode(utf8_encode($site['site_og']), true);

	foreach($og as $param => $value)
		Tpl::Meta($param, Core::FixText(html_entity_decode($value)), 'property');
}

#####################################################
## OTROS
#####################################################

Tpl::MoreHTML('itemscope');
Tpl::MoreHead('prefix', 'og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#');

// Definir información útil para la cabecera.
Tpl::Set('head_mhtml', Tpl::$MHTML);
Tpl::Set('head_mhead', Tpl::$MHEAD);
Tpl::Set('head_metas', Tpl::$METAS);
Tpl::Set('head_stuff', Tpl::$STUFF);
Tpl::Set('head_vars', Tpl::$VARS);
Tpl::Set('head_styles', Tpl::$STYLES);
Tpl::Set('head_js', Tpl::$JS);
Tpl::Set('head_lang', $HLANG);

// Constantes definidas.
$constants = get_defined_constants(true);
$constants = $constants['user'];

// Definir variables de plantilla para todas las constantes.
Tpl::Set($constants);
// Definir variables de configuración de sitio.
Tpl::Set($site);
?>