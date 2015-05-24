<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

if (!class_exists("CMbCodeSniffer")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMbCodeSniffer-error-PEAR_needed");
  return;
}

$sniffer = new CMbCodeSniffer;
$files = $sniffer->getFilesTree();
//unset($files["modules"]);
$count = CMbArray::countLeafs($files);

$reports = $sniffer->checkReports($files);
$stats = $sniffer->buildStats($files);
$types = $stats["-root-"];

$existing_reports = $reports;
CMbArray::removeValue("none", $existing_reports);
$existing_count = count($existing_reports);

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruct() anyhow
$sniffer->__destruct();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("files", $files);
$smarty->assign("count", $count);
$smarty->assign("types", $types);
$smarty->assign("reports", $reports);
$smarty->assign("existing_count", $existing_count);
$smarty->assign("stats", $stats);
$smarty->display("sniff_code.tpl");
