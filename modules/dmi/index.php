<?php /* $Id: index.php 4929 2008-10-06 11:03:21Z mytto $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 4929 $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_categories", null , TAB_READ);
$module->registerTab("vw_elements"  , null , TAB_READ);

?>