<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_planning"   , "Planning"               , TAB_EDIT);
$module->registerTab("vw_edit_planning"  , "Edition d'interventions", TAB_EDIT);
$module->registerTab("vw_edit_sejour"    , "Edition de séjours"     , TAB_READ);
$module->registerTab("vw_edit_urgence"   , "Edition d'urgences"     , TAB_READ);
$module->registerTab("vw_protocoles"     , "Liste des protocoles"   , TAB_EDIT);
$module->registerTab("vw_edit_protocole" , "Edition de protocoles"  , TAB_EDIT);
$module->registerTab("vw_edit_typeanesth", "Types d'anesthesie"     , TAB_EDIT);

?>