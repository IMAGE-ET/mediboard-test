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
if ($mode == "html") {
  $miner = new COperationWorkflow();
  $miner->warnUsage();
}

$deblist = CValue::getOrSession("deblistbloc", CMbDT::date("-1 DAY"));
$finlist = max(CValue::get("finlistbloc", $deblist), $deblist);
$bloc_id = CValue::getOrSession("bloc_id");
$type    = CValue::get("type", "prevue");

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
    $csvName = "stats_bloc_".$deblist."_".$finlist."_".$bloc_id.".csv";
    $csvPath = "tmp/$csvName";
    $csvFile = fopen($csvPath, "w") or die("can't open file");
    $title  = '"Date";"Salle prévue";"Salle réelle";"Début vacation";"Fin vacation";"N° d\'ordre prévu";"N° d\'ordre réel";';
    $title .= '"Patient";"Prise en charge";"Chirurgien";"Anesthésiste";"Libellé";"DP";"CCAM";"Type d\'anesthésie";"Code ASA";"Placement programme";';
    $title .= '"Entrée salle";"Début d\'induction";"Fin d\'induction";"Début d\'intervention";"Fin d\'intervention";"Sortie salle";"Patient suivant";';
    $title .= '"Entrée reveil";"Sortie reveil"
';

    fwrite($csvFile, $title);
    foreach ($operations as $_operation) {
        $line  = '"'.CMbDT::date($_operation->_datetime).'";';
        $line .= '"'.$_operation->_ref_salle_prevue.'";';
        $line .= '"'.$_operation->_ref_salle_reelle.'";';
        $line .= '"'.$_operation->_deb_plage.'";';
        $line .= '"'.$_operation->_fin_plage.'";';
        $line .= '"'.$_operation->rank.'";';
        $line .= '"'.$_operation->_rank_reel.'";';
        $line .= '"'.$_operation->_ref_sejour->_ref_patient->_view.'" ('.$_operation->_ref_sejour->_ref_patient->_age.');';
        $line .= '"'.$_operation->_ref_sejour->type.'";';
        $line .= '"'.$_operation->_ref_chir->_view.'";';
        $line .= '"'.$_operation->_ref_anesth->_view.'";';
        $line .= '"'.$_operation->libelle.'";';
        $line .= '"'.$_operation->_ref_sejour->DP.'";';
        $line .= '"'.$_operation->codes_ccam.'";';
        $line .= '"'.$_operation->_lu_type_anesth.'";';
        $line .= '"'.$_operation->ASA.'";';
        $line .= '"'.$_operation->_ref_workflow->date_creation.'";';
        $line .= '"'.$_operation->entree_salle.'";';
        $line .= '"'.$_operation->induction_debut.'";';
        $line .= '"'.$_operation->induction_fin.'";';
        $line .= '"'.$_operation->debut_op.'";';
        $line .= '"'.$_operation->fin_op.'";';
        $line .= '"'.$_operation->sortie_salle.'";';
        $line .= '"'.$_operation->_pat_next.'";';
        $line .= '"'.$_operation->entree_reveil.'";';
        $line .= '"'.$_operation->sortie_reveil_possible.'"
';
        fwrite($csvFile, $line);
    }
    fclose($csvFile);
  
    
    header("Pragma: ");
    header("Cache-Control: ");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    // END extra headers to resolve IE caching bug
  
    header("MIME-Version: 1.0");
    header("Content-length: ".filesize($csvPath));
    header("Content-type: text/csv; charset=iso-8859-1");
    header("Content-disposition: attachment; filename=\"".$csvName."\"");
    readfile($csvPath);
    return;
}
else {
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("deblist",    $deblist);
  $smarty->assign("finlist",    $finlist);
  $smarty->assign("blocs",  $blocs);
  $smarty->assign("plages",     $plages);
  $smarty->assign("operations", $operations);
  $smarty->assign("nb_interv" , $nb_interv);
  $smarty->assign("bloc",       $bloc);
  $smarty->assign("type",       $type);

  $smarty->display("vw_bloc2.tpl");
}
