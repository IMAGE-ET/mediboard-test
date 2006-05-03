<?php /* $Id: code_selector.php,v 1.15 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 1.15 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// @ todo : pourquoi on arrive pas à y accéder dès que le module n'est pas visible ???
//if (!$canRead) {
//  $AppUI->redirect( "m=system&a=access_denied" );
//}

require_once( $AppUI->getModuleClass('dPccam', 'acte') );
require_once( $AppUI->getModuleClass('dPcim10', 'codecim10') );

$list = array();
$type = dPgetParam( $_GET, 'type', 0 );
$chir = dPgetParam( $_GET, 'chir', 0 );

switch($type) {
	case 'ccam' :
  case 'ccam2':
		$sql = "select favoris_code
				from ccamfavoris
				where favoris_user = '$chir' or favoris_user = $AppUI->user_id
				group by favoris_code
				order by favoris_code";
		$codes = db_loadlist($sql);
		$i = 0;
    foreach($codes as $key => $value) {
      $list[$i] = new CCodeCCAM($value["favoris_code"]);
      $list[$i]->loadLite();
      $i++;
    }
		break;


	default : {
		$sql = "select favoris_code
				from cim10favoris
				where favoris_user = '$chir' or favoris_user = '$AppUI->user_id'
				order by favoris_code";
		$codes = db_loadlist($sql);
    $i = 0;
    foreach($codes as $key => $value) {
      $list[$i] = new CCodeCIM10($value["favoris_code"]);
      $list[$i]->loadLite();
      $list[$i]->libelleLong = $list[$i]->libelle;
      $i++;
    }
    break;
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('type', $type);
$smarty->assign('list', $list);

$smarty->display('code_selector.tpl');

?>