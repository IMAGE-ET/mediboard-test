<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: $
* @author Thomas Despoix
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_identifiants", "Identifiants Santé400", TAB_EDIT);
$module->registerTab("synchro_sante400", "Intégration santé 400", TAB_READ);

?>