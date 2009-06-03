<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("form_print_planning"        , null, TAB_READ);
$module->registerTab("edit_sorties"               , null, TAB_READ);
$module->registerTab("vw_recherche"               , null, TAB_READ);
$module->registerTab("vw_affectations"            , null, TAB_READ);
$module->registerTab("vw_idx_pathologies"         , null, TAB_READ);
/*
$module->registerTab("vw_idx_sejour"              , null, TAB_READ);
if(CModule::getActive("dPprescription")){
$module->registerTab("vw_bilan_prescription"      , null, TAB_READ);
}
*/
$module->registerTab("vw_suivi_bloc"              , null, TAB_READ);
$module->registerTab("vw_idx_chambres"            , null, TAB_ADMIN);
?>