<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$rpu_id         = CValue::get("rpu_id");

// Chargement du RPU
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejourMutation();

// Chargement du séjour
$sejour = $rpu->loadRefSejour();
$sejour->loadRefPatient()->loadIPP();
$sejour->loadNDA();
$sejour->loadRefsConsultations();

// Horaire par défaut
if (!$sejour->sortie_reelle) {
	$sejour->sortie_reelle = mbDateTime();
}

$listLits = array();
$ljoin = array();
$where = array();

$ljoin["affectation"] = "affectation.lit_id = lit.lit_id";

$where["affectation.entree"] = "<= '$sejour->sortie_reelle'";
$where["affectation.sortie"] = ">= '$sejour->sortie_reelle'";
$where["affectation.function_id"] = "IS NOT NULL";

$lit = new CLit();
$listLits = $lit->loadList($where, null, null, null, $ljoin);
foreach($listLits as $_lit){
	$_lit->loadRefChambre()->loadRefService();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu", $rpu);
$smarty->assign("sejour", $sejour);
$smarty->assign("listLits", $listLits);

$smarty->display("inc_edit_sortie.tpl");
?>