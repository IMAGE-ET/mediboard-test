<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_operations" , "Salles d'opération", TAB_READ);
$module->registerTab("vw_reveil"     , "Salle de reveil"   , TAB_READ);
//$module->registerTab("vw_brancardage", "Brancardage"       , TAB_READ);
$module->registerTab("vw_urgences"   , "Liste des urgences", TAB_READ);
//$module->registerTab("vw_anesthesie" , "Anesthésie"        , TAB_READ);

?>