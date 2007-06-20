<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

//require_once("../dPccam/acteccam.class.php")
require_once($AppUI->getModuleClass("dPsalleOp", "acteccam"));

// @ todo : pourquoi on arrive pas à y accéder dès que le module n'est pas visible ???
//$can->needsRead();

$list = array();
$list2 = array();

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


//Appel de la fonction listant les codes les plus utilisés pour un praticien 
  $actes = new CActeCCAM();
  $codes = $actes->getFavoris($chir);
  $i = 0;
 
  
  // mbTrace($codes);
  
  foreach($codes as $key => $value) {
    $list2[$i]["codeccam"] = new CCodeCCAM($value["code_acte"]);
    $list2[$i]["occ"] = $value["nb_acte"];
    $list2[$i]["codeccam"]->loadLite();
    $i++;
  }

  //mbTrace($list2);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type", $type);
$smarty->assign("list", $list);
$smarty->assign("list2", $list2);

$smarty->display("code_selector.tpl");

?>