<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();

$tabs[] = array("vw_idx_patients" , "Chercher un dossier"        , 0);
$tabs[] = array("vw_full_patients", "Consulter un dossier"       , 0);
$tabs[] = array("vw_edit_patients", "Edition d'un dossier"       , 1);
$tabs[] = array("vw_medecins"     , "Médecins correspondants"    , 1);

$default = "vw_idx_patients";
?>