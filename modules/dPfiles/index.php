<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_files"       , "Visualiser les fichiers", TAB_READ);
$module->registerTab("configure"      , "Gérer les catégories"   , TAB_ADMIN);
$module->registerTab("files_integrity", "Vérification fichiers"  , TAB_ADMIN);

?>