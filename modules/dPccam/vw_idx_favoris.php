<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

CAppUI::requireModuleClass("dPsalleOp", "acteccam");

$class = CValue::get("class","");

$can->needsRead();
$list = array();

$user = $AppUI->user_id;

$actes = new CActeCCAM();
$codes = $actes->getFavoris($user,$class,"");
$i = 0;

$codesByChap = CFavoriCCAM::getOrdered($user,$class);

foreach($codes as $key => $value) {	
  $code = CCodeCCAM::get($value["code_acte"], CCodeCCAM::LITE);
  $code->getChaps();
  
  $code->favoris_id = 0;
  $code->occ = $value["nb_acte"];
  $code->class = $value["object_class"];
  $chapitre =& $code->chapitres[0];
  $list[$chapitre["code"]]["nom"] = $chapitre["nom"];
  $list[$chapitre["code"]]["codes"][$value["code_acte"]]= $code;
}

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


$favoris = new CFavoriCCAM();
$favoris->filter_class = $class;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("favoris", $favoris);
$smarty->assign("list", $list);
$smarty->assign("fusion", $fusion);
$smarty->assign("codesByChap", $codesByChap);
$smarty->display("vw_idx_favoris.tpl");

?>