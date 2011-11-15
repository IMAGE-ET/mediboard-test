<?php /* $Id: prat_import_csv.php 6103 2009-04-16 13:36:52Z yohann $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU GPL
 */

CCanDo::checkAdmin();

$file = isset($_FILES['import']) ? $_FILES['import'] : null;

$results = array();
$i = 0;

if ($file && ($fp = fopen($file['tmp_name'], 'r'))) {
  // Object columns on the first line
  $cols = fgetcsv($fp, null, ";");

  // Each line
  while($line = fgetcsv($fp, null, ";")) {
    if (!isset($line[0]) || $line[0] == "") {
      continue;
    }
    
    $results[$i]["nom"]     = trim(CMbString::removeDiacritics($line[1]));
    $results[$i]["bloc"] = trim(CMbString::removeDiacritics($line[0]));
    
    // Bloc
    $bloc = new CBlocOperatoire();
    $bloc->nom      = $results[$i]["bloc"];
    $bloc->group_id = CGroups::loadCurrent()->_id;
    $bloc->loadMatchingObject();
    if(!$bloc->_id) {
      $msg = $bloc->store();
      if($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      } else {
        CAppUI::setMsg("Bloc créé", UI_MSG_OK);
      }
    }
    
    // Salle
    $salle = new CSalle();
    $salle->nom = $results[$i]["nom"];
    $salle->bloc_id = $bloc->_id;
    $salle->loadMatchingObject();
    if(!$chambre->_id) {
      $salle->stats = 1;
      $salle->dh    = 0;
      $msg = $salle->store();
      if($msg) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      } else {
        CAppUI::setMsg("Salle créée", UI_MSG_OK);
      }
    }
    
    $i++;
  }
  fclose($fp);
}

CAppUI::callbackAjax('$("systemMsg").insert', CAppUI::getMsg());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("salles_import_csv.tpl");
