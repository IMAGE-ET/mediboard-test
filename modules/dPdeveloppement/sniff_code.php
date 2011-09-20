<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

CCanDo::checkRead();

if (!class_exists("CMbCodeSniffer")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMbCodeSniffer-error-PEAR_needed");
  return;
}

$sniffer = new CMbCodeSniffer;
$files = $sniffer->getFilesTree();
$reports = $sniffer->checkReports($files);

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruct() anyhow
$sniffer->__destruct();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("files", $files);
$smarty->assign("reports", $reports);
$smarty->display("sniff_code.tpl");

?>