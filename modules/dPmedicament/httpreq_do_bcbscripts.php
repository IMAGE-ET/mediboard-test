<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$libDir = "lib/bcb";
$libPath = "$libDir/bcb.zip";
$user = "mediboard";
$pass = CValue::get("password");

$libURL = "http://$user:$pass@www.mediboard.org/bcb/objet-bcb-php-v1-92.zip";

switch ($action = CValue::get("action")) {
  // Test BCB Scripts existence
  case "test":
  CAppUI::stepAjax("BCBScripts-running_ok", UI_MSG_OK, $action);
  
  if (!is_dir("lib/bcb")) {
    CAppUI::stepAjax("BCBScripts-dirko", UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("BCBScripts-dirok", UI_MSG_WARNING);
  
  break;
  
  // Install BCB Scripts
  case "install":
  CAppUI::stepAjax("BCBScripts-running_ok", UI_MSG_OK, $action);
    
  if (false == $content = file_get_contents($libURL)) {
    CAppUI::stepAjax("BCBScripts-download_ko", UI_MSG_ERROR);
  }

  CAppUI::stepAjax("BCBScripts-download_ok", UI_MSG_OK, mbConvertDecaBinary(strlen($content)));
  
  CMbPath::forceDir($libDir);
  if (!CMbPath::emptyDir($libDir)) {
    CAppUI::stepAjax("BCBScripts-emptydir_ko", UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("BCBScripts-emptydir_ok", UI_MSG_OK);
  
  file_put_contents($libPath, $content);
  if (false == $count = CMbPath::extract($libPath, $libDir)) {
    CAppUI::stepAjax("BCBScripts-extract_ko", UI_MSG_ERROR);
  }

  CAppUI::stepAjax("BCBScripts-extract_ok", UI_MSG_OK, $count);
  
  break;
  
  default:
  CAppUI::stepAjax("BCBScripts-running_ko", UI_MSG_ERROR, $action);
}

?>