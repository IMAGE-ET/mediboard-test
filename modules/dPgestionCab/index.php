<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("edit_compta", "Comptabilité" , TAB_READ);
$module->registerTab("edit_paie"  , "Fiche de paie", TAB_READ);
$module->registerTab("edit_params", "Paramètres"   , TAB_READ);

?>