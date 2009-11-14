<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("form_print_planning"        , TAB_READ);
$module->registerTab("edit_sorties"               , TAB_READ);
$module->registerTab("vw_recherche"               , TAB_READ);
$module->registerTab("vw_affectations"            , TAB_READ);
$module->registerTab("vw_idx_pathologies"         , TAB_READ);
/*
$module->registerTab("vw_idx_sejour"              , TAB_READ);
if(CModule::getActive("dPprescription")){
$module->registerTab("vw_bilan_prescription"      , TAB_READ);
}
*/
$module->registerTab("vw_suivi_bloc"              , TAB_READ);
$module->registerTab("vw_idx_chambres"            , TAB_ADMIN);
?>