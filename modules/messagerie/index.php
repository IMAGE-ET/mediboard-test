<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//internal
if ($dPconfig["messagerie"]["enable_internal"]) {
  $module->registerTab("vw_list_internalMessages", TAB_READ);
}

//external
if ($dPconfig["messagerie"]["enable_external"]) {
  //$module->registerTab("vw_list_externalMessages", TAB_READ);
}

//direct access to POP/IMAP (admin only)
$module->registerTab("vw_list_POPMessages", TAB_ADMIN);

//$module->registerTab("write_usermessage" , TAB_READ);

?>
