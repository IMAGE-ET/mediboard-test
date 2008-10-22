<?php /* $Id: httpreq_vw_list_antecedents.php 1476 2007-01-19 16:40:49Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1476 $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

$sejour_id = mbGetValueFromGetOrSession("sejour_id", 0);

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du dossier medical
$sejour->loadRefDossierMedical();
$dossier_medical =& $sejour->_ref_dossier_medical;

// Chargement des antecedents et traitements
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("inc_list_ant_anesth.tpl");

?>