<?php /* $Id: labo_groupage.php,v 1.5 2006/04/28 13:32:24 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPpmsi', 'GHM') );

$operation_id  = mbGetValueFromGet("operation_id", null);

$GHM = new CGHM();

$age  = mbGetValueFromGetOrSession("age", null);
$sexe = mbGetValueFromGetOrSession("sexe", null);
$DP   = mbGetValueFromGetOrSession("DP", null);
$DR   = mbGetValueFromGetOrSession("DR", null);

$DASs = mbGetValueFromGetOrSession("DASs", array());
if(is_array($DASs)) {
  foreach($DASs as $key => $DAS) {
    if($DAS == "")
      unset($DASs[$key]);
  }
}

$DADs = mbGetValueFromGetOrSession("DADs", array());
if(is_array($DADs)) {
  foreach($DADs as $key => $DAD) {
    if($DAD == "")
      unset($DADs[$key]);
  }
}

$actes = mbGetValueFromGetOrSession("actes", array());
if(is_array($actes)) {
  foreach($actes as $key => $acte) {
    if($acte["code"] == "")
      unset($actes[$key]);
  }
}

$type_hospi  = mbGetValueFromGetOrSession("type_hospi", null);
$duree       = mbGetValueFromGetOrSession("duree", null);
$seances     = mbGetValueFromGetOrSession("seances", null);
$motif       = mbGetValueFromGetOrSession("motif", null);
$destination = mbGetValueFromGetOrSession("destination", null);

// Remplissage des champs du GHM
$GHM->age         = $age;
$GHM->sexe        = $sexe;
$GHM->DP          = $DP;
$GHM->DR          = $DR;
$GHM->DASs        = $DASs;
$GHM->DADs        = $DADs;
$GHM->actes       = $actes;
$GHM->type_hospi  = $type_hospi;
$GHM->duree       = $duree;
$GHM->motif       = $motif;
$GHM->seances     = $seances;
$GHM->destination = $destination;

// Liaison avec l'opération
if($operation_id) {
  $GHM->bindOp($operation_id);
}

$GHM->getGHM();

//mbTrace($GHM);

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign("GHM", $GHM);

$smarty->display('labo_groupage.tpl');

?>