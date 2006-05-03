<?php /* $Id: orl_put_praticiens.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('mediusers') );

$sql = "SELECT * FROM import_praticiens";
$listImport = db_loadlist($sql);

$new = 0;
$link = 0;

foreach($listImport as $key => $value) {
  $sql = "SELECT * FROM users, users_mediboard" .
  		"\nWHERE users.user_id = users_mediboard.user_id" .
  		"\nAND users.user_first_name = '".trim($value["prenom"])."'" .
  		"\nAND users.user_last_name = '".trim($value["nom"])."'";
  $match = db_loadlist($sql);
  if(!count($match)) {
  	$user = new CMediusers;
  	// DB Table key
  	$user->user_id = '';
  	// DB Fields
    $user->remote = 0;
    // DB References
    // On prend la fonction ORL
	$user->function_id = 11;
    // dotProject user fields
    $user->_user_type = 3;
	$user->_user_username = trim($value["nom"]);
	$user->_user_password = "nevousconnectezpassvp";
	$user->_user_first_name = trim($value["prenom"]);
	$user->_user_last_name  = trim($value["nom"]);
	$user->store();
	$sql = "UPDATE import_praticiens" .
    		"\nSET mb_id = '".$user->user_id."'" .
    		"\nWHERE praticien_id = '".$value["praticien_id"]."'";
    db_exec($sql);
    $new++;
  } else {
    $sql = "UPDATE import_praticiens" .
    		"\nSET mb_id = '".$match[0]["user_id"]."'" .
    		"\nWHERE praticien_id = '".$value["praticien_id"]."'";
    db_exec($sql);
    $link++;
  }
}

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr>';

?>