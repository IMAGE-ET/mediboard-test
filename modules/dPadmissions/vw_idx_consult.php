<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

//Initialisations des variables
$date         = mbGetValueFromGetOrSession("date", mbDate());
$today        = mbDate();
$hour         = mbTime(null);
$board        = mbGetValueFromGet("board", 1);
$boardItem    = mbGetValueFromGet("boardItem", 1);
$consult      = new CConsultation;


// Récupération des fonctions
$cabinets = CMediusers::loadFonctions();

// Récupération de la liste des anesthésistes
$mediuser = new CMediusers;
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);


if($consult->consultation_id) {
  $date = $consult->_ref_plageconsult->date;
  mbSetValueToSession("date", $date);
}


// Récupération des plages de consultation du jour et chargement des références
$listPlages = array();
foreach($anesthesistes as $anesth) {
  $listPlages[$anesth->_id]["anesthesiste"] = $anesth;
  $listPlage = new CPlageconsult();
  $where = array();
  $where["chir_id"] = "= '$anesth->_id'";
  $where["date"] = "= '$date'";
  $order = "debut";
  $listPlage = $listPlage->loadList($where, $order);
  if(count($listPlage)) {
    $listPlages[$anesth->_id]["plages"] = $listPlage;
  } else {
    unset($listPlages[$anesth->_id]);
    unset($anesthesistes[$anesth->_id]);
  }
}

foreach($listPlages as &$element) {
  foreach ($element["plages"] as &$plage) {
    $plage->_ref_chir =& $element["anesthesiste"];
    $plage->loadRefsBack();
    foreach ($plage->_ref_consultations as $keyConsult => &$consultation) {
      if (($consultation->chrono == CConsultation::TERMINE)) {
        unset($plage->_ref_consultations[$keyConsult]);
        continue;
      }
			$consultation->loadRefSejour();
      $consultation->loadRefPatient();
      $consultation->loadRefCategorie();
      $consultation->countDocItems();
    }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"       ,$consult);
$smarty->assign("listPlages"    ,$listPlages);
$smarty->assign("date"          ,$date);
$smarty->assign("today"         ,$today);
$smarty->assign("hour"          ,$hour);
$smarty->assign("anesthesistes" ,$anesthesistes);
$smarty->assign("cabinets"      ,$cabinets);
$smarty->assign("board"         ,$board);
$smarty->assign("boardItem"     ,$boardItem);
$smarty->assign("canCabinet"    ,CModule::getCanDo("dPcabinet"));

$smarty->display("vw_idx_consult.tpl");


?>