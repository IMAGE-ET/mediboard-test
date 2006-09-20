<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_dossier"   , "Dossiers patient"          , TAB_READ);
$module->registerTab("edit_actes"   , "Codage des actes"          , TAB_READ);
$module->registerTab("labo_groupage", "Groupage GHM"              , TAB_READ);
$module->registerTab("vw_list_hospi", "Liste des hospitalisations", TAB_READ);

?>