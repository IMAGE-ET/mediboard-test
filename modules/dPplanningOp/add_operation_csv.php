<?php 

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU GPL
 */

CCanDo::checkAdmin();

set_time_limit(240);

$file    = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array(
  "count_ok"     => 0,
  "count_nda_nt" => 0,
  "count_erreur" => 0,
);

$bloc  = new CBlocOperatoire();
$blocs = $bloc->loadGroupList();

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
      
    // Parsing
    $NDA        = trim($line[0]);
    $ADELI      = trim($line[1]);
    $date_debut = mbDateTime(trim($line[2]));
    $date_fin   = mbDateTime(trim($line[3]));
    $libelle    = CMbString::capitalize(addslashes(trim($line[4])));
    $nom_salle  = addslashes(trim($line[5]));
    $cote       = isset($line[6]) ? addslashes(trim($line[6])) : null;;
    
    // Traitement du séjour
    $sejour = new CSejour();
    $sejour->loadFromNDA($NDA);

    if (!$sejour->_id) {
      CAppUI::stepAjax("Le sejour n'a pas été retrouvé dans Mediboard par le NDA : '$NDA'", UI_MSG_WARNING);
      $results["count_nda_nt"]++;
      continue;
    }
    
    // Traitement du praticien responsable de l'intervention
    $mediuser        = new CMediusers();
    $mediuser->adeli = $ADELI;
    $count           = $mediuser->countMatchingList();
    if ($count == "0") {
      CAppUI::stepAjax("L'utilisateur '$ADELI' n'a pas été retrouvé dans Mediboard", UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }
    elseif ($count > 1) {
      CAppUI::stepAjax("Plusieurs utilisateurs correspondent à cette recherche", UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }    
    $mediuser->loadMatchingObject();
    
    // Traitement de la date/heure début, et durée de l'opération
    $date_op  = mbDate($date_debut);
    $time_op  = mbTime($date_debut);
    $temps_op = mbSubTime(mbTime($date_debut), mbTime($date_fin)); 
    
    // Recherche de la salle
    $salle      = new CSalle();
    $where["nom"]     = "= '$nom_salle'";
    $where["bloc_id"] = CSQLDataSource::prepareIn(array_keys($blocs));
    if (!$salle->loadObject($where)) {
      CAppUI::stepAjax("La salle '$nom_salle' n'a pas été retrouvée dans Mediboard", UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }
    
    // Recherche d'une éventuelle PlageOp
    $plageOp           = new CPlageOp();
    $plageOp->chir_id  = $mediuser->_id;
    $plageOp->salle_id = $salle->_id;
    $plageOp->date     = $date_op;
    foreach ($plageOp->loadMatchingList() as $_plage) {
      // Si notre intervention est dans la plage Mediboard
      if ($_plage->debut <= $time_op && $temps_op <= $_plage->fin) {
        $plageOp = $_plage;
        
        break;
      }
    }

    // Recherche d'une intervension existante sinon création
    $operation                 = new COperation();
    $operation->sejour_id      = $sejour->_id;
    $operation->chir_id        = $mediuser->_id;
    $operation->plageop_id     = $plageOp->_id;
    $operation->salle_id       = $salle->_id;
    if (!$operation->plageop_id) {
      $operation->date         = $date_op;
    }
    $operation->temp_operation = $temps_op;
    $operation->time_operation = $time_op;
    $operation->loadMatchingObject();
    
    $operation->libelle        = $libelle;
    $operation->cote           = $cote ? $cote : "inconnu";
    
    if ($msg = $operation->store()) {
      CAppUI::stepAjax($msg, UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }
    
    $results["count_ok"]++;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("results", $results);
$smarty->display("add_operation_csv.tpl");

?>