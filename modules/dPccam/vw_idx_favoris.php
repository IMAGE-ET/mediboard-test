<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

require_once($AppUI->getModuleClass("dPsalleOp", "acteccam"));

$can->needsRead();
$list = array();

$user = $AppUI->user_id;

$actes = new CActeCCAM();
$codes = $actes->getFavoris($user,"","");
$i = 0;

$codesByChap = CFavoriCCAM::getOrdered($user);

foreach($codes as $key => $value) {
  $code = new CCodeCCAM($value["code_acte"]);
  $code->loadLite();
  $code->loadChaps();
  $code->favoris_id = 0;
  $code->occ = $value["nb_acte"];
  $chapitre =& $code->chapitres[0];
  $list[$chapitre["code"]]["nom"] = $chapitre["nom"];
  $list[$chapitre["code"]]["codes"][$value["code_acte"]]= $code;
}

//mbTrace($list);

$fusion = $list;

//Fusion des deux tableaux
foreach($codesByChap as $keychapter => $chapter){
	if (!array_key_exists($keychapter, $fusion)) {
		$fusion[$keychapter] = $chapter;
		continue;
	} 
	
	foreach($chapter["codes"] as $keycode => $code)
	if (!array_key_exists($keycode, $fusion[$keychapter]["codes"])) {
		$fusion[$keychapter]["codes"][$keycode] = $code;
		continue;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list", $list);
$smarty->assign("fusion", $fusion);
$smarty->assign("codesByChap", $codesByChap);
$smarty->display("vw_idx_favoris.tpl");

?>