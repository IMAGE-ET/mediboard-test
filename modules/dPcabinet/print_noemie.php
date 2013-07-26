<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());

$consult = new CConsultation();

$where = array();
$ljoin["plageconsult"]                      = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where["consultation.du_tiers"]             = "> 0";
$where["consultation.tiers_date_reglement"] = "IS NULL";
$where["plageconsult.date"]                 = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

// Filtre sur les praticiens
$chir_id = CValue::getOrSession("chir");
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$order = "plageconsult.date";

/** @var CConsultation[] $listConsults */
$listConsults = $consult->loadList($where, $order, null, null, $ljoin);

$total = array("nb" => 0, "value" => 0);

foreach ($listConsults as $consult) {
  $consult->loadRefsFwd();
  $consult->loadRefsReglements();
  
  // Chargment de la FSE
  if (CModule::getActive("fse")) {
    if ($fse = CFseFactory::createFSE()) {
      $fse->loadIdsFSE($consult);
    }
  }
  
  // Retour Noemie déjà traité
  $hasNoemie = (!$consult->_current_fse || $consult->_current_fse->S_FSE_ETAT != 9);
  if ($hasNoemie) {
    unset($listConsults[$consult->_id]);
    continue;
  }
  
  // Nouveau règelement pour le formulaire
  $consult->_new_reglement_tiers = new CReglement();
  $consult->_new_reglement_tiers->setObject($consult);
  $consult->_new_reglement_tiers->mode = "virement";
  $consult->_new_reglement_tiers->montant = $consult->_du_restant_tiers;

  // Toaux
  $total["nb"]++;
  $total["value"] += $consult->_du_restant_tiers;
}

// Création du template

$smarty = new CSmartyDP();

$smarty->assign("listConsults", $listConsults);
$smarty->assign("total"       , $total);
$smarty->assign("_date_min"   , $filter->_date_min);
$smarty->assign("_date_max"   , $filter->_date_max);
$smarty->assign("listPrat"    , $listPrat);

$smarty->display("print_noemie.tpl");
