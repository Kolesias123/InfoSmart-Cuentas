<?
$page['require_login'] = true;
require '../../Init.php';

$result = array();

AcUsers::DeletePhotoAccess();
$result['status'] = 'OK';

echo json_encode($result);
?>