<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_admission", "Consultation des admissions", TAB_READ);
$module->registerTab("vw_idx_sortie"   , "Validation des sorties"     , TAB_READ);

?>