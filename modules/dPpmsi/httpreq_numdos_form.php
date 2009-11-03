<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsEdit();

$sejour_id = CValue::getOrSession("sejour_id");


// Chargement du dossier patient
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefPatient();

if ($sejour->_id) {
  $sejour->loadNumDossier();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_sejour"         , $sejour );
$smarty->assign("patient"         , $sejour->_ref_patient );
$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->display("inc_numdos_form.tpl");