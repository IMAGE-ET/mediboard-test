<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_list_usermessages", TAB_READ);
//$module->registerTab("write_usermessage" , TAB_READ);

?>
