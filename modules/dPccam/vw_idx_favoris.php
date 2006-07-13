<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}
	
require_once( $AppUI->getModuleClass('dPccam', 'acte') );

$user = $AppUI->user_id;

//Recherche des codes favoris
$query = "SELECT favoris_id, favoris_code
		  FROM ccamfavoris
		  WHERE favoris_user = '$AppUI->user_id'
		  ORDER BY favoris_code";
$favoris = db_loadList($query);

$i = 0;
$codes = array();
foreach($favoris as $key => $value) {
  $codes[$i] = new CCodeCCAM($value["favoris_code"]);
  $codes[$i]->loadLite();
  $codes[$i]->favoris_id = $value["favoris_id"];
  $i++;
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('codes', $codes);

$smarty->display('vw_idx_favoris.tpl');

?>