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
$date_min = CValue::getOrSession("_date_min", CMbDT::date());
$date_max = CValue::getOrSession("_date_max", CMbDT::date());
$chir_id  = CValue::getOrSession("chir");

$taux_factures = array();
$list_taux = array();

// Filtre sur les praticiens
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

$where = array();
$where["ouverture"] = "BETWEEN '$date_min' AND '$date_max'";
$where["facture_cabinet.praticien_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));

$facture = new CFactureCabinet();

$list_taux = explode("|", CAppUI::conf("dPcabinet CConsultation default_taux_tva"));
foreach ($list_taux as $taux) {
  $where["taux_tva"] = " = '$taux'";
  $factures = $facture->loadList($where, "ouverture, praticien_id");
  $taux_factures[$taux] = $factures;
}

$total_tva = 0;
$nb_factures = 0;
foreach ($taux_factures as $taux => $factures) {
  $nb_factures += count($factures);
  $total = $totalht = $totalttc = $totalst = 0;

  foreach ($factures as $facture) {
    $facture->loadRefPatient();
    $facture->loadRefPraticien();
    $facture->loadRefsObjects();
    $facture->loadRefsReglements();

    $total    += $facture->du_tva;
    $totalht  += ($facture->_montant_avec_remise - $facture->du_tva);
    $totalttc += $facture->_montant_avec_remise;
    $totalst  += $facture->_secteur3;
  }

  $taux_factures[$taux] = array();
  $taux_factures[$taux]["count"] = count($factures);
  $taux_factures[$taux]["total"] = $total;
  $taux_factures[$taux]["factures"] = $factures;
  $taux_factures[$taux]["totalst"]  = $totalst;
  $taux_factures[$taux]["totalht"]  = $totalht;
  $taux_factures[$taux]["totalttc"] = $totalttc;

  $total_tva += $total;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("taux_factures", $taux_factures);
$smarty->assign("total_tva",     $total_tva);
$smarty->assign("nb_factures",   $nb_factures);
$smarty->assign("list_taux",     $list_taux);
$smarty->assign("date_min",      $date_min);
$smarty->assign("date_max",      $date_max);
$smarty->assign("listPrat",      $listPrat);

$smarty->display("print_tva.tpl");