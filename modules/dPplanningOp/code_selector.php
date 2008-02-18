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

$list         = array();
$list2        = array();
$listAnesth   = array();
$list2Anesth  = array();
$fusion       = array();
$fusionAnesth = array();
$fusionCim    = array();

$type         = mbGetValueFromGet("type", 0 );
$chir         = mbGetValueFromGet("chir", 0 );
$anesth       = mbGetValueFromGet("anesth", 0 );
$object_class = mbGetValueFromGet("object_class", 0 );
$view         = mbGetValueFromGet("view", "alpha");

switch($type) {
	case "ccam" :
  case "ccam2":
  	$condition = "(favoris_user = '$chir' OR favoris_user = '$AppUI->user_id')";
  	if($object_class != "") { 
  	  $condition .= " AND object_class = '$object_class'";
  	}
		$sql = "select favoris_code
				from ccamfavoris
				where $condition
				group by favoris_code
				order by favoris_code";
		$codes = $ds->loadlist($sql);

    foreach($codes as $key => $value) {
      $list[$value["favoris_code"]]["codeccam"] = CCodeCCAM::get($value["favoris_code"], CCodeCCAM::MEDIUM);
      $list[$value["favoris_code"]]["codeccam"]->occ = "0";
    }
    
    if($anesth) {
	  	$condition = "favoris_user = '$anesth'";
	  	if($object_class != "") { 
	  	  $condition .= " AND object_class = '$object_class'";
	  	}
			$sql = "select favoris_code
					from ccamfavoris
					where $condition
					group by favoris_code
					order by favoris_code";
			$codes = $ds->loadlist($sql);
	
	    foreach($codes as $key => $value) {
	      $listAnesth[$value["favoris_code"]]["codeccam"] = CCodeCCAM::get($value["favoris_code"], CCodeCCAM::MEDIUM);
	      $listAnesth[$value["favoris_code"]]["codeccam"]->occ = "0";
      }
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
    $list2[$value["code_acte"]]["codeccam"] = CCodeCCAM::get($value["code_acte"], CCodeCCAM::MEDIUM);
    $list2[$value["code_acte"]]["codeccam"]->occ = $value["nb_acte"];
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
  
  if($anesth) {
	  //Appel de la fonction listant les codes les plus utilisés pour un praticien 
	  $actes = new CActeCCAM();
	  $codes = $actes->getFavoris($anesth, $object_class, $view);
	
	  foreach($codes as $key => $value) {
	    $list2Anesth[$value["code_acte"]]["codeccam"] = CCodeCCAM::get($value["code_acte"], CCodeCCAM::MEDIUM);
	    $list2Anesth[$value["code_acte"]]["codeccam"]->occ = $value["nb_acte"];
	  }
	
	  // Fusion des 2 tableaux
	  $fusionAnesth = $list2Anesth;
	 
	  foreach($listAnesth as $keycode => $code){
	  	if(!array_key_exists($keycode, $fusionAnesth)) {
	  		$fusionAnesth[$keycode] = $code;
	  		continue;
	  	}
	  }
	 
	  // si tri par ordre alphabetique selectionne
	  if($view=="alpha") {
	    sort($fusionAnesth);
	  }
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("view"        ,$view);
$smarty->assign("type"        , $type);
$smarty->assign("list"        , $list);
$smarty->assign("list2"       , $list2);
$smarty->assign("listAnesth"  , $listAnesth);
$smarty->assign("list2Anesth" , $list2Anesth);
$smarty->assign("fusion"      , $fusion);
$smarty->assign("fusionAnesth", $fusionAnesth);
$smarty->assign("fusionCim"   , $fusionCim);
$smarty->assign("object_class", $object_class);
$smarty->assign("chir"        , $chir);
$smarty->assign("anesth"      , $anesth);
$smarty->display("code_selector.tpl");

?>