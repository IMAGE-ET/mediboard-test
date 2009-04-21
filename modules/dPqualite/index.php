<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision$
 *  @author Sébastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_incident"           , null , TAB_READ);
$module->registerTab("vw_incidentvalid"      , null , TAB_READ);
$module->registerTab("vw_edit_ei"            , null , TAB_ADMIN);
$module->registerTab("vw_stats"              , null , TAB_ADMIN);
$module->registerTab("vw_procedures"         , null , TAB_READ);
$module->registerTab("vw_procencours"        , null , TAB_EDIT);
$module->registerTab("vw_procvalid"          , null , TAB_ADMIN);
$module->registerTab("vw_edit_classification", null , TAB_ADMIN);
$module->registerTab("vw_modeles"            , null , TAB_EDIT);

?>