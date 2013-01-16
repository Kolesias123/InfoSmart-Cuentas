<?

$data = array(
	1 => array(
		'image' 	=> 'http://localhost/resources/accounts/images/pre/screen.accounts.popups.png',
		'title' 	=> 'Una prueba de titulo',
		'content' 	=> 'Una prueba de contenido'
	),

	2 => array(
		'image' 	=> 'http://localhost/resources/accounts/images/pre/screen.accounts.popups.png',
		'title' 	=> 'Una prueba de titulo 2',
		'content' 	=> 'Una prueba de contenido 3'
	)
);

echo json_encode($data);

?>