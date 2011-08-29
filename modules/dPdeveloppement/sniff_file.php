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

$file = CValue::get("file");
$file = str_replace(":", "/", $file);

$sniffer = new CMbCodeSniffer;
echo "<pre>";
$sniffer->process($file);
$sniffer->report($file, "full");
echo "</pre>";

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruct() anyhow
$sniffer->__destruct();
  
return;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("files", $files);
$smarty->display("sniff_file.tpl");

?>