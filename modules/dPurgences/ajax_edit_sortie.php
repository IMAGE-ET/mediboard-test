<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$rpu_id = CValue::get("rpu_id");

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
  $sejour->sortie_reelle = CMbDT::dateTime();
}

$where = array();
$where["entree"] = "<= '$sejour->sortie_reelle'";
$where["sortie"] = ">= '$sejour->sortie_reelle'";
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach ($blocages_lit as $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";

  if ($affectation->loadObject($where)) {
    $affectation->loadRefSejour();
    $affectation->_ref_sejour->loadRefPatient();
    $blocage->_ref_lit->_view .= " indisponible jusqu'à ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectation->_ref_sejour->_ref_patient->_view.")";
  }
}

$list_mode_sortie = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
  $mode_sortie = new CModeSortieSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_sortie = $mode_sortie->loadGroupList($where);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rpu", $rpu);
$smarty->assign("sejour", $sejour);
$smarty->assign("blocages_lit", $blocages_lit);
$smarty->assign("list_mode_sortie", $list_mode_sortie);

$smarty->display("inc_edit_sortie.tpl");
