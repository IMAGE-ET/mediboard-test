<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

// @ todo : pourquoi on arrive pas à y accéder dès que le module n'est pas visible ???
//$can->needsRead();

$list = array();
$type = mbGetValueFromGet("type", 0 );
$chir = mbGetValueFromGet("chir", 0 );

switch($type) {
	case "ccam" :
  case "ccam2":
		$sql = "select favoris_code
				from ccamfavoris
				where favoris_user = '$chir' or favoris_user = '$AppUI->user_id'
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
$smarty = new CSmartyDP();

$smarty->assign("type", $type);
$smarty->assign("list", $list);

$smarty->display("code_selector.tpl");

?>