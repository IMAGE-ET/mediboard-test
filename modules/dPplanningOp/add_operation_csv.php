<?php 

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU GPL
 */

CCanDo::checkAdmin();

$file    = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array();
$i       = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    $results[$i]["error"] = 0;
    
    // Parsing
    $results[$i]["NDA"]          = $NDA        = trim($line[0]);
    $results[$i]["ADELI"]        = $ADELI      = trim($line[1]);
    $results[$i]["date_debut"]   = $date_debut = mbDateTime(trim($line[2]));
    $results[$i]["date_fin"]     = $date_fin   = mbDateTime(trim($line[3]));
    $results[$i]["libelle"]      = $libelle    = CMbString::capitalize(addslashes(trim($line[4])));
    $results[$i]["salle"]        = $nom_salle  = addslashes(trim($line[5]));
    $results[$i]["cote"] = $cote = isset($line[6]) ? addslashes(trim($line[6])) : null;;
    
    
    // Traitement du séjour
    $sejour = new CSejour();
    $sejour->loadFromNDA($NDA);
    
    if (!$sejour->_id) {
      $results[$i]["error"] = "Le sejour n'a pas été retrouvé dans Mediboard par le NDA : '$NDA'";
      $i++;
      continue;
    }
    
    // Traitement du praticien responsable de l'intervention
    $mediuser        = new CMediusers();
    $mediuser->adeli = $ADELI;
    $count           = $mediuser->countMatchingList();
    if ($count == "0") {
      $results[$i]["error"] = "L'utilisateur n'a pas été retrouvé dans Mediboard";
      $i++;
      continue;
    }
    elseif ($count > 1) {
      $results[$i]["error"] = "Plusieurs utilisateurs correspondent à cette recherche";
      $i++;
      continue;
    }    
    $mediuser->loadMatchingObject();
    
    // Traitement de la date/heure début, et durée de l'opération
    $date_op  = mbDate($date_debut);
    $time_op  = mbTime($date_debut);
    $temps_op = mbSubTime(mbTime($date_debut), mbTime($date_fin)); 
    
    // Recherche de la salle
    $salle      = new CSalle();
    $salle->nom = $nom_salle;
    if (!$salle->loadMatchingObject()) {
      $results[$i]["error"] = "La salle '$nom_salle' n'a pas été retrouvée dans Mediboard";
      $i++;
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
    $operation->cote           = $cote ? $cote : "inconnu";
    
    if ($msg = $operation->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      $results[$i]["error"] = $msg;
      $i++;
      continue;
    }
    
    $i++;
  }
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("results", $results);
$smarty->display("add_operation_csv.tpl");

?>