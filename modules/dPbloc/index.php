<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning_week"     , "Planning de la semaine"   , TAB_EDIT);
$module->registerTab("vw_edit_planning"     , "Planning du jour"         , TAB_EDIT);
$module->registerTab("vw_edit_interventions", "Gestion des interventions", TAB_EDIT);
$module->registerTab("vw_urgences"          , "Voir les urgences"        , TAB_EDIT);
$module->registerTab("vw_idx_materiel"      , "Commande de matériel"     , TAB_EDIT);
$module->registerTab("vw_idx_salles"        , "Gestion des salles"       , TAB_EDIT);
$module->registerTab("print_planning"       , "Impression des plannings" , TAB_READ);

?>