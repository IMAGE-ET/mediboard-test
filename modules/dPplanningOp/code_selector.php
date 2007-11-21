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
$ds = CSQLDataSource::get("std");

// @ todo : pourquoi on arrive pas à y accéder dès que le module n'est pas visible ???
//$can->needsRead();

$list = array();
$list2 = array();
$fusion = array();
$fusionCim = array();

$type = mbGetValueFromGet("type", 0 );
$chir = mbGetValueFromGet("chir", 0 );
$object_class = mbGetValueFromGet("object_class", 0 );
$view = mbGetValueFromGet("view", "alpha");

switch($type) {
	case "ccam" :
  case "ccam2":
  	$condition=($object_class=="")?"favoris_user = '$chir' or favoris_user = '$AppUI->user_id'":
  	"(favoris_user = '$chir' or favoris_user = '$AppUI->user_id') and object_class = '$object_class'";
		$sql = "select favoris_code
				from ccamfavoris
				where $condition
				group by favoris_code
				order by favoris_code";
		$codes = $ds->loadlist($sql);

    foreach($codes as $key => $value) {
      $list[$value["favoris_code"]]["codeccam"] = new CCodeCCAM($value["favoris_code"]);
      $list[$value["favoris_code"]]["codeccam"]->loadMedium();
      $list[$value["favoris_code"]]["codeccam"]->occ = "0";
    }
  
    break;


	default : {
		$sql = "select favoris_code
				from cim10favoris
				where favoris_user = '$chir' or favoris_user = '$AppUI->user_id'
				order by favoris_code";
		$codes = $ds->loadlist($sql);
		
    foreach($codes as $key => $value) {
      $list[$value["favoris_code"]]["codecim"] = new CCodeCIM10($value["favoris_code"]);
      $list[$value["favoris_code"]]["codecim"]->loadLite();
      $list[$value["favoris_code"]]["codecim"]->occ = "0";
    }
    break;
  }
}
    
if($type=="cim10"){
  // Chargement des codes cim les plus utilsé par le praticien $chir
  $code = new CCodeCIM10();
  
  $sql = "SELECT DP, count(DP) as nb_code
          FROM `sejour`
          WHERE sejour.praticien_id = '$chir'
          AND DP IS NOT NULL
          AND DP != ''
          GROUP BY DP
          ORDER BY count(DP) DESC
          LIMIT 10;";

  $listCodes = $ds->loadList($sql);

  $listCimStat = array();
 
  foreach($listCodes as $key => $value) {
    $listCimStat[$value["DP"]]["codecim"] = new CCodeCIM10($value["DP"]);
    $listCimStat[$value["DP"]]["codecim"]->loadLite();
    $listCimStat[$value["DP"]]["codecim"]->occ = $value["nb_code"];
  }
  
  // Fusion des deux tableaux de favoris
  $fusionCim = $listCimStat;
 
  foreach($list as $keycode => $code){
  	if(!array_key_exists($keycode, $fusionCim)) {
  		$fusionCim[$keycode] = $code;
  		continue;
  	}
  }
  
  // si tri par ordre alphabetique selectionne
  if($view=="alpha") {
    sort($fusionCim);
  }
}


if($type=="ccam"){
  //Appel de la fonction listant les codes les plus utilisés pour un praticien 
  $actes = new CActeCCAM();
  $codes = $actes->getFavoris($chir,$object_class, $view);

  foreach($codes as $key => $value) {
    $list2[$value["code_acte"]]["codeccam"] = new CCodeCCAM($value["code_acte"]);
    $list2[$value["code_acte"]]["codeccam"]->loadMedium();
    $list2[$value["code_acte"]]["codeccam"]->occ = $value["nb_acte"];;
  }

  // Fusion des 2 tableaux
  $fusion = $list2;
 
  foreach($list as $keycode => $code){
  	if(!array_key_exists($keycode, $fusion)) {
  		$fusion[$keycode] = $code;
  		continue;
  	}
  }
 
  // si tri par ordre alphabetique selectionne
  if($view=="alpha") {
    sort($fusion);
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("view",$view);
$smarty->assign("type", $type);
$smarty->assign("list", $list);
$smarty->assign("fusionCim", $fusionCim);
$smarty->assign("fusion", $fusion);
$smarty->assign("list2", $list2);
$smarty->assign("object_class", $object_class);
$smarty->assign("chir" , $chir);
$smarty->display("code_selector.tpl");

?>