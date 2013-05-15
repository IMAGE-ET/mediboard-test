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

CApp::setTimeLimit(600);

$sniffer = new CMbCodeSniffer;
$files = $sniffer->getFilesTree();
//unset($files["modules"]);

$reports = $sniffer->checkReports($files);
$stats = $sniffer->buildStats($files);
$types = $stats["-root-"];
//mbTrace($stats, "Stats");
//mbTrace($types, "Types");

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruct() anyhow
$sniffer->__destruct();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("files", $files);
$smarty->assign("types", $types);
$smarty->assign("reports", $reports);
$smarty->assign("stats", $stats);
$smarty->display("sniff_code.tpl");

?>