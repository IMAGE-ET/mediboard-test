<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

require "PHP/CodeSniffer.php";
$root_dir = CAppUI::conf("root_dir");

$verbosity = 1;
$tabwidth = 2;
$files = "$root_dir/classes/mbobject.class.php";
$standard = "Mediboard";
$local = true; // Not recursive;
$sniffs = array();

$sniffer = new PHP_CodeSniffer($verbosity, $tabwidth);
$sniffer->process($files, $standard, $sniffs, $local);

// Cuz sniffer changes work dir but restores it at destruction
// Be aware that unset() won't call __destruc() anyhow
$sniffer->__destruct();

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("sniff_code.tpl");

?>