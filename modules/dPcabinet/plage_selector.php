<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Initialisation des variables
$plageconsult_id = dPgetParam( $_GET, 'plageconsult_id');
$date = dPgetParam( $_GET, 'date', mbDate() );
$ndate = mbDate("+1 MONTH", $date);
$pdate = mbDate("-1 MONTH", $date);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();

// Praticiens sélectionnés
$chir_id = dPgetParam( $_GET, 'chir_id');
if (!$chir_id) {
  $listChir = new CMediusers;
  $listChir = $listChir->loadPraticiens(PERM_EDIT);
  $inChir = join(array_keys($listChir), ", ");
}

$where["chir_id"] = $chir_id ? "= '$chir_id'" : "IN ($inChir)";

// Choix du mois
$month = mbTranformTime(null, $date, "%Y-%m-__");
$where["date"] = "LIKE '$month'";
$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);
foreach ($listPlage as $keyPlage => $valuePlage) {
  if (!$plageconsult_id && $date == $valuePlage->date) {
    $plageconsult_id = $valuePlage->plageconsult_id;
  }
  
  $listPlage[$keyPlage]->loadRefs(false);
}

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
$plage->_ref_chir = new CMediusers;
$listPlace = array();
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $plage->loadRefs(false);
  $date = $plage->date;
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $listPlace[$i]["time"] = mbTime("+ $minutes minutes", $plage->debut);
    $listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient();
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($listPlace[($keyPlace + $i)])) {
        $listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('date', $date);
$smarty->assign('ndate', $ndate);
$smarty->assign('pdate', $pdate);
$smarty->assign('chir_id', $chir_id);
$smarty->assign('plageconsult_id', $plageconsult_id);
$smarty->assign('plage', $plage);
$smarty->assign('listPlage', $listPlage);
$smarty->assign('listPlace', $listPlace);

$smarty->display('plage_selector.tpl');

?>