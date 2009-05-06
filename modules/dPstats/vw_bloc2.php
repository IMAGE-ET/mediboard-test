<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

//set_time_limit(180);
//ini_set("memory_limit", "512M");

$mode = mbGetValueFromGet("mode", "html");

$deblist = mbGetValueFromGetOrSession("deblistbloc", mbDate("-1 DAY"));
$finlist = $deblist;
$finlist = max(mbGetValueFromGet("finlistbloc", $deblist), $deblist);
$bloc_id = mbGetValueFromGetOrSession("bloc_id");

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listBlocs = CGroups::loadCurrent()->loadBlocs();
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

$where = array();
$where["stats"] = "= '1'";
if($bloc->_id) {
  $where["bloc_id"] = "= '$bloc->_id'";
}
$salle = new CSalle;
$listSalles = $salle->loadGroupList($where);

// Récupération des plages
$where = array(
  "date"     => "BETWEEN '$deblist 00:00:00' AND '$finlist 23:59:59'",
  "salle_id" => CSQLDataSource::prepareIn(array_keys($listSalles)),
);
$order = "date, salle_id, debut, chir_id";

$plage = new CPlageOp;
$listPlages = $plage->loadList($where, $order);

// Récupération des interventions
foreach($listPlages as &$curr_plage) {
  $curr_plage->loadRefsFwd(1);
  $curr_plage->loadRefsBack(0, "entree_salle");
  
  $i = 1;
  foreach($curr_plage->_ref_operations as &$curr_op) {
    $curr_op->_rank_reel = $curr_op->entree_salle ? $i : "";
    $i++;
    $next = next($curr_plage->_ref_operations);
    $curr_op->_pat_next = (($next !== false) ? $next->entree_salle : null);
    $curr_op->loadRefsFwd(1);
    $curr_op->loadLogs();
    $curr_op->_ref_sejour->loadRefsFwd(1);
  }
}

if($mode == "csv") {
    // A utiliser comme ça :
    // m=dPstats&dialog=1&a=vw_bloc2&mode=text&suppressHeaders=1
    $csvName = "stats_bloc_".$deblist."_".$finlist."_".$bloc_id.".csv";
    $csvPath = "tmp/$csvName";
    $csvFile = fopen($csvPath, "w") or die("can't open file");
    $title  = '"Date";"Bloc";"Salle";"Début vacation";"Fin vacation";"N° d\'ordre prévu";"N° d\'ordre réel";';
    $title .= '"Patient";"Prise en charge";"Chirurgien";"Anesthésiste";"Libellé";"DP";"CCAM";"Type d\'anesthésie";"Code ASA";"Placement programme";';
    $title .= '"Entrée salle";"Début d\'induction";"Fin d\'induction";"Début d\'intervention";"Fin d\'intervention";"Sortie salle";"Patient suivant";';
    $title .= '"Entrée reveil";"Sortie reveil"
';
    fwrite($csvFile, $title);
    foreach($listPlages as $curr_plage) {
      foreach($curr_plage->_ref_operations as $curr_op) {
        $line  = '"'.$curr_plage->date.'";';
        $line .= '"'.$curr_plage->_ref_salle->_ref_bloc->_view.'";';
        $line .= '"'.$curr_plage->_ref_salle->_view.'";';
        $line .= '"'.$curr_plage->debut.'";';
        $line .= '"'.$curr_plage->fin.'";';
        $line .= '"'.$curr_op->rank.'";';
        $line .= '"'.$curr_op->_rank_reel.'";';
        $line .= '"'.$curr_op->_ref_sejour->_ref_patient->_view.'";';
        $line .= '"'.$curr_op->_ref_sejour->type.'";';
        $line .= '"'.$curr_op->_ref_chir->_view.'";';
        $line .= '"'.$curr_op->_ref_anesth->_view.'";';
        $line .= '"'.$curr_op->libelle.'";';
        $line .= '"'.$curr_op->_ref_sejour->DP.'";';
        $line .= '"'.$curr_op->codes_ccam.'";';
        $line .= '"'.$curr_op->_lu_type_anesth.'";';
        $line .= '"'.$curr_op->_ref_consult_anesth->ASA.'";';
        $line .= '"'.$curr_op->_ref_first_log->date.'";';
        $line .= '"'.$curr_op->entree_salle.'";';
        $line .= '"'.$curr_op->induction_debut.'";';
        $line .= '"'.$curr_op->induction_fin.'";';
        $line .= '"'.$curr_op->debut_op.'";';
        $line .= '"'.$curr_op->fin_op.'";';
        $line .= '"'.$curr_op->sortie_salle.'";';
        $line .= '"'.$curr_op->_pat_next.'";';
        $line .= '"'.$curr_op->entree_reveil.'";';
        $line .= '"'.$curr_op->sortie_reveil.'"
';
        fwrite($csvFile, $line);
      }
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
} else {
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("deblist",    $deblist);
  $smarty->assign("finlist",    $finlist);
  $smarty->assign("listBlocs",  $listBlocs);
  $smarty->assign("listPlages", $listPlages);
  $smarty->assign("bloc",       $bloc);

  $smarty->display("vw_bloc2.tpl");
}

?>