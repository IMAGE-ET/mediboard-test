<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("form_print_planning", "Impression plannings"  , TAB_READ);
$module->registerTab("edit_sorties"       , "Déplacements / Sorties", TAB_READ);
$module->registerTab("vw_recherche"       , "Chercher une chambre"  , TAB_READ);
$module->registerTab("vw_affectations"    , "Affectations"          , TAB_READ);
$module->registerTab("vw_idx_chambres"    , "Chambres"              , TAB_ADMIN);
$module->registerTab("vw_idx_services"    , "Services"              , TAB_ADMIN);

?>