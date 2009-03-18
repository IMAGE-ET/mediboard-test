<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

CAppUI::requireModuleClass("dPsalleOp", "acteccam");

$ds = CSQLDataSource::get("std");

$type         = mbGetValueFromGet("type");
$mode         = mbGetValueFromGet("mode", "stats");
$order        = mbGetValueFromGet("order", "taux");
$chir         = mbGetValueFromGet("chir");
$anesth       = mbGetValueFromGet("anesth");
$object_class = mbGetValueFromGet("object_class");

$profiles = array (
  "chir"   => $chir,
  "anesth" => $anesth,
  "user"   => $AppUI->user_id,
);

if ($profiles["user"] == $profiles["anesth"] || $profiles["user"] == $profiles["chir"]) {
	unset($profiles["user"]);
}

if (!$profiles["anesth"]) {
	unset($profiles["anesth"]);
}


$listByProfile = array();
$users = array();
foreach ($profiles as $profile => $user_id) {
  // Chargement du user du profile
  $user = new CMediusers();
  $user->load($user_id);
  $users[$profile] = $user;
  
  $list = array();
	if ($type == "ccam") {
	  /**
	   * Favoris
	   */
	  if ($mode == "favoris") {
			$condition = "favoris_user = '$user_id'";
			if ($object_class != "") { 
			  $condition .= " AND object_class = '$object_class'";
			}
			
			$sql = "select favoris_code
					from ccamfavoris
					where $condition
					group by favoris_code
					order by favoris_code";
			$codes = $ds->loadlist($sql);
			
		  foreach ($codes as $key => $value) {
		    // Attention à bien cloner le code CCAM car on rajoute une champ à la volée
		    $list[$value["favoris_code"]] = CCodeCCAM::get($value["favoris_code"], CCodeCCAM::MEDIUM);
		    $list[$value["favoris_code"]]->occ = "0";
		  }
		  
		  sort($list);  
	  }
	  
	
	  /**
	   *  Statistiques
	   */
	  if ($mode == "stats") {
	  
		  // Appel de la fonction listant les codes les plus utilisés pour un praticien 
		  $actes = new CActeCCAM();
		  $codes = $actes->getFavoris($user_id, $object_class);
		
		  foreach ($codes as $key => $value) {
		    // Attention à bien cloner le code CCAM car on rajoute une champ à la volée
		    $list[$value["code_acte"]] = CCodeCCAM::get($value["code_acte"], CCodeCCAM::MEDIUM);
		    $list[$value["code_acte"]]->occ = $value["nb_acte"];
		  }
		  
		  if ($order == "alpha") {
		    sort($list);
		  }
	  }
	}
	    
	if ($type=="cim10") {
	  /**
	   * Favoris
	   */
	  if ($mode == "favoris") {
		  $sql = "select favoris_code
					from cim10favoris
					where favoris_user = '$user_id'
					order by favoris_code";
			$codes = $ds->loadlist($sql);
			
		   foreach($codes as $key => $value) {
		     $list[$value["favoris_code"]] = new CCodeCIM10($value["favoris_code"]);
		     $list[$value["favoris_code"]]->loadLite();
		     $list[$value["favoris_code"]]->occ = "0";
		   }
	  
	  }
	
	  /**
	   *  Statistiques
	   */
	  if ($mode == "stats") {
	    // Chargement des codes cim les plus utilsé par le praticien $chir
		  $code = new CCodeCIM10();
		  
		  $sql = "SELECT DP, count(DP) as nb_code
		          FROM `sejour`
		          WHERE sejour.praticien_id = '$user_id'
		          AND DP IS NOT NULL
		          AND DP != ''
		          GROUP BY DP
		          ORDER BY count(DP) DESC
		          LIMIT 50;";
		
		  $listCodes = $ds->loadList($sql);
		
		  $list = array();
		 
		  foreach($listCodes as $key => $value) {
		    $list[$value["DP"]] = new CCodeCIM10($value["DP"]);
		    $list[$value["DP"]]->loadLite();
		    $list[$value["DP"]]->occ = $value["nb_code"];
		  }
	  }
	}
	
	$listByProfile[$profile] = $list;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("type"        , $type);
$smarty->assign("mode"        , $mode);
$smarty->assign("order"       , $order);
$smarty->assign("object_class", $object_class);
$smarty->assign("chir"        , $chir);
$smarty->assign("anesth"      , $anesth);
$smarty->assign("users"       , $users);
$smarty->assign("listByProfile", $listByProfile);
$smarty->display("code_selector.tpl");

?>