<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_mediusers"  , "Utilisateurs"              , TAB_READ);
$module->registerTab("vw_idx_functions"  , "Fonctions des utilisateurs", TAB_READ);
$module->registerTab("vw_idx_disciplines", "Spécialités médicales"     , TAB_READ);

?>