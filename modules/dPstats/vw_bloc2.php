<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkRead();

$mode = CValue::get("mode", "html");
$deblist = CValue::getOrSession("deblistbloc", CMbDT::date("-1 DAY"));
$finlist = max(CValue::get("finlistbloc", $deblist), $deblist);
$bloc_id = CValue::getOrSession("bloc_id");
$type    = CValue::get("type", "prevue");
CView::enforceSlave();

if ($mode == "html") {
  $miner = new COperationWorkflow();
  $miner->warnUsage();
}


$blocs = CGroups::loadCurrent()->loadBlocs();
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

$where = array();
$where["stats"] = "= '1'";
if ($bloc->_id) {
  $where["bloc_id"] = "= '$bloc->_id'";
}

$salle = new CSalle();
$salles = $salle->loadGroupList($where);

// Récupération des plages
$where = array(
  "date"     => "BETWEEN '$deblist 00:00:00' AND '$finlist 23:59:59'",
  "salle_id" => CSQLDataSource::prepareIn(array_keys($salles)),
);
/** @var CPlageOp[] $plages */
$plages = array();
/** @var COperation[] $operations */
$operations = array();
/** @var int $nb_interv */
$nb_interv = 1;

if ($type == "prevue") {
  $plage = new CPlageOp();
  $order = "date, salle_id, debut, chir_id";
  $plages = $plage->loadList($where, $order);
  CStoredObject::massLoadFwdRef($plages, "chir_id");
  CStoredObject::massLoadFwdRef($plages, "spec_id");

  // Récupération des interventions
  foreach ($plages as $_plage) {
    $_plage->loadRefOwner();
    $_plage->loadRefAnesth();
    $_plage->loadRefSalle();
    $_plage->loadRefsOperations(false, "entree_salle");

    $nb_interv = 1;
    foreach ($_plage->_ref_operations as $_operation) {
      // Calcul du rang
      $_operation->_rank_reel = $_operation->entree_salle ? $nb_interv : "";
      $nb_interv++;
      $next = next($_plage->_ref_operations);
      $_operation->_pat_next = (($next !== false) ? $next->entree_salle : null);

      $_operation->_ref_plageop = $_plage;

      $operations[$_operation->_id] = $_operation;
    }
  }
}
else {
  // Récupération des interventions
  $where = array();
  $where[] = "date BETWEEN '$deblist' AND '$finlist'";
  $where[] = "salle_id ".CSQLDataSource::prepareIn(array_keys($salles))." OR salle_id IS NULL";

  if ($type != "all") {
    $where["plageop_id"] = "IS NULL";
  }
  $order = "date, salle_id, chir_id";
  $operation = new COperation();
  $operations = $operation->loadList($where, $order);

  foreach ($operations as $_operation) {
    // Calcul du rang
    $_operation->_rank_reel = $_operation->entree_salle ? $nb_interv : "";
    $nb_interv++;
    $_operation->_pat_next = null;
  }
}

// Chargement exhaustif
CStoredObject::massLoadFwdRef($operations, "anesth_id");
CStoredObject::massLoadFwdRef($operations, "chir_id");
$sejours = CStoredObject::massLoadFwdRef($operations, "sejour_id");
CStoredObject::massLoadFwdRef($sejours, "patient_id");
foreach ($operations as $_operation) {
  $_operation->updateDatetimes();
  $_operation->loadRefAnesth();
  $_operation->loadRefPlageOp();
  $_operation->updateSalle();
  $_operation->loadRefChir()->loadRefFunction();
  $_operation->loadRefPatient();
  $_operation->loadRefWorkflow();

  // Ajustements ad hoc
  if ($plage = $_operation->_ref_plageop) {
    $_operation->_ref_salle_prevue = $plage->_ref_salle;
    $_operation->_ref_salle_reelle = $_operation->_ref_salle;
    $_operation->_deb_plage = $plage->debut;
    $_operation->_fin_plage = $plage->fin;
  }
  else {
    $_operation->_ref_salle_prevue = $_operation->_ref_salle;
    $_operation->_ref_salle_reelle = $_operation->_ref_salle;
    $_operation->_deb_plage = $_operation->date;
    $_operation->_fin_plage = $_operation->date;
  }
}


if ($mode == "csv") {
  $csvName = "stats_bloc_".$deblist."_".$finlist."_".$bloc_id;
  $csv = new CCSVFile();
  $title = array("Date", "Salle prévue","Salle réelle","Début vacation","Fin vacation","N° d\'ordre prévu","N° d\'ordre réel",
  "Patient","Prise en charge","Chirurgien","Anesthésiste","Libellé","DP","CCAM","Type d\'anesthésie","Code ASA",
  "Placement programme","Entrée bloc","Entrée salle","Début d\'induction","Fin d\'induction","Début d\'intervention",
  "Fin d\'intervention","Sortie salle","Patient suivant","Entrée reveil","Sortie reveil");

  $csv->writeLine($title);

  foreach ($operations as $_operation) {

    $line_op = array( CMbDT::date($_operation->_datetime), $_operation->_ref_salle_prevue, $_operation->_ref_salle_reelle,
      $_operation->_deb_plage, $_operation->_fin_plage, $_operation->rank, $_operation->_rank_reel,
      $_operation->_ref_sejour->_ref_patient->_view.'('.$_operation->_ref_sejour->_ref_patient->_age.')',
      $_operation->_ref_sejour->type, $_operation->_ref_chir->_view, $_operation->_ref_anesth->_view, $_operation->libelle,
      $_operation->_ref_sejour->DP, $_operation->codes_ccam, $_operation->_lu_type_anesth, $_operation->ASA,
      $_operation->_ref_workflow->date_creation, $_operation->entree_bloc, $_operation->entree_salle, $_operation->induction_debut,
      $_operation->induction_fin, $_operation->debut_op, $_operation->fin_op, $_operation->sortie_salle, $_operation->_pat_next,
      $_operation->entree_reveil, $_operation->sortie_reveil_possible);
    $csv->writeLine($line_op);
  }

  $csv->stream($csvName);
  return;
}
else {
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("deblist"   , $deblist);
  $smarty->assign("finlist"   , $finlist);
  $smarty->assign("blocs"     , $blocs);
  $smarty->assign("plages"    , $plages);
  $smarty->assign("operations", $operations);
  $smarty->assign("nb_interv" , $nb_interv);
  $smarty->assign("bloc"      , $bloc);
  $smarty->assign("type"      , $type);

  $smarty->display("vw_bloc2.tpl");
}
