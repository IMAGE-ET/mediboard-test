<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();
// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());

$chir_id = CValue::getOrSession("chir", null);
$chirSel = new CMediusers;
$chirSel->load($chir_id);
if (CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $chirSel->loadPraticiens(PERM_EDIT);
}
else {
  $listPrat = $chirSel->loadProfessionnelDeSante(PERM_EDIT);
}

$where = array();
$ljoin["plageconsult"]                      = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where["consultation.du_tiers"]             = "> 0";
$where["consultation.tiers_date_reglement"] = "IS NULL";
$where["plageconsult.date"]                 = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
$where["plageconsult.chir_id"]              = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);
$order = "plageconsult.date";

$listConsults = array();
$consult = new CConsultation();
$listConsults = $consult->loadList($where, $order, null, null, $ljoin);

$total = array("nb" => 0, "value" => 0);

foreach ($listConsults as $consult) {
  $consult->loadRefsFwd();
  $consult->loadRefsReglements();

  if (CModule::getActive("fse")) {
    if ($fse = CFseFactory::createFSE()) {  
      $fse->loadIdsFSE($consult);
    }
  }
  
  $consult->_new_reglement_tiers = new CReglement();
  $consult->_new_reglement_tiers->setObject($consult);
  $consult->_new_reglement_tiers->mode = "virement";
  $consult->_new_reglement_tiers->montant = $consult->_du_restant_tiers;

  $hasNoemie = (!$consult->_current_fse || $consult->_current_fse->S_FSE_ETAT != 9);
  if (!$hasNoemie) {
    $_POST["consultation_id"] = $consult->_id;
    $_POST["montant"]         = $consult->_du_restant_tiers;
    $do = new CDoObjectAddEdit("CReglement", "reglement_id");
    $do->redirect = null;
    $do->doIt();
  }
}

// Redirection finale
$do->redirect = "m=$m&a=print_noemie&dialog=1";
$do->doRedirect();

?>