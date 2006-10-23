<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning"      , "Programme de consultation", TAB_READ);
$module->registerTab("edit_planning"    , "Prise de rendez-vous"      , TAB_READ);
$module->registerTab("edit_consultation", "Consultation"              , TAB_READ);
$module->registerTab("vw_dossier"       , "Dossiers"                  , TAB_READ);
$module->registerTab("form_print_plages", "Impression plannings"      , TAB_READ);
$module->registerTab("vw_compta"        , "Comptabilité"              , TAB_READ);

?>