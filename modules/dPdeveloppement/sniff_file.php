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

$file = CValue::get("file");
$file = str_replace(":", "/", $file);

// Has to be a file, not a directory
if (!is_file($file)) {
  CAppUI::stepAjax("sniff-file-nofile-error", UI_MSG_ERROR, $file);
}

$sniffer = new CMbCodeSniffer;
$sniffer->process($file);
$sniffer->report($file);
$stats = $sniffer->stat($file);
$errors = reset($sniffer->getFilesErrors());
$alerts = $sniffer->getFlattenAlerts();

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruct() anyhow
$sniffer->__destruct();
  
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("file", $file);
$smarty->assign("alerts", $alerts);
$smarty->assign("errors", $errors);
$smarty->display("sniff_file.tpl");
