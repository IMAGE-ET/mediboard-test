<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('mediusers') );

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// L'utilisateur est-il praticien?
$chir = null;
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$vue = mbGetValueFromGetOrSession("vue1");

// Praticien selectionné
$chirSel = mbGetValueFromGetOrSession("chirSel", $chir ? $chir->user_id : null);

// Plage de consultation selectionnée
$plageconsult_id = mbGetValueFromGetOrSession("plageconsult_id");
$plageSel = new CPlageconsult();
$plageSel->load($plageconsult_id);
$plageSel->loadRefs();
foreach($plageSel->_ref_consultations as $key => $value) {
  if ($vue && $plageSel->_ref_consultations[$key]->paye)
    unset($plageSel->_ref_consultations[$key]);
  else {
    $plageSel->_ref_consultations[$key]->loadRefPatient();
    $plageSel->_ref_consultations[$key]->loadRefsDocs();
  }
}

if ($plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  mbSetValueToSession("plageconsult_id", $plageconsult_id);
  $plageSel = new CPlageconsult();
}

// Liste des chirurgiens
$mediusers = new CMediusers();
$listChirs = $mediusers->loadPraticiens(PERM_EDIT);

// Période
$today = mbDate();
$debut = mbGetValueFromGetOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Sélection des plages
$plage = new CPlageconsult();
$where["chir_id"] = "= '$chirSel'";
for($i = 0; $i < 7; $i++) {
  $date = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  $plagesPerDay = $plage->loadList($where);
  foreach($plagesPerDay as $key => $value) {
    $plagesPerDay[$key]->loadRefs(false);
  }
  $plages[$date] = $plagesPerDay;
}

// Liste des heures
$listHours = array();
for($i = 8; $i <= 20; $i++) {
  $listHours[$i] = $i;
}

// Liste des minutes
$listMins = array();
$listMins[] = "00";
$listMins[] = "30";

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('vue', $vue);
$smarty->assign('chirSel', $chirSel);
$smarty->assign('plageSel', $plageSel);
$smarty->assign('listChirs', $listChirs);
$smarty->assign('plages', $plages);
$smarty->assign('today', $today);
$smarty->assign('debut', $debut);
$smarty->assign('fin', $fin);
$smarty->assign('prec', $prec);
$smarty->assign('suiv', $suiv);
$smarty->assign('listHours', $listHours);
$smarty->assign('listMins', $listMins);

$smarty->display('vw_planning.tpl');

?>