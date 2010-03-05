<?php /* $Id: vw_soap_services.php 6141 2009-04-21 14:19:23Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: 6141 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Recherche de l'établissement
$group = new CGroups;
if ($idClinique = CValue::get("idClinique")) {
	$idexClinique = new CIdSante400;
	$idexClinique->object_class= "CGroups";
	$idexClinique->tag = "eCap";
	$idexClinique->id400 = $idClinique;
	$group = $idexClinique->getMbObject();
}

// Recherche du séjour
$sejour = new CSejour;
if ($idDHE = CValue::get("idDHE")) {
	$idexSejour = new CIdSante400;
	$idexSejour->object_class= "CSejour";
	$idexSejour->tag = "eCap DHE CIDC:$idClinique";
	$idexSejour->id400 = $idDHE;
	$sejour = $idexSejour->getMbObject();
}

if ($actionType == "a" && $sejour->_id && $group->_id) {
	$view = CValue::get("view");
  CAppUI::redirect("g={$group->_id}&m=ssr&a=vw_aed_sejour_ssr&sejour_id={$sejour->_id}#$view");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("idClinique", $idClinique);
$smarty->assign("idDHE", $idDHE);
$smarty->assign("group", $group);
$smarty->assign("sejour", $sejour);

$smarty->display("vw_ssr.tpl");

?>