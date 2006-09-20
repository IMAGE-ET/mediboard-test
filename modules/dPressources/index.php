<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain OLLIVIER
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_planning", "Planning réservations"    , TAB_READ);
$module->registerTab("edit_planning", "Administration des plages", TAB_EDIT);
$module->registerTab("view_compta"  , "Comptabilité"             , TAB_EDIT);

?>