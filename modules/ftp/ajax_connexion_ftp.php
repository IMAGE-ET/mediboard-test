<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'échange spécifié", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name, "ftp", false, null, false);

$ftp = new CFTP();
$ftp->init($exchange_source);

try {
  $ftp->testSocket();
  CAppUI::stepAjax("CFTP-success-connection", E_USER_NOTICE, $ftp->hostname, $ftp->port);

  $ftp->connect();
  CAppUI::stepAjax("CFTP-success-authentification", E_USER_NOTICE, $ftp->username);

  if ($ftp->passif_mode) {
    CAppUI::stepAjax("CFTP-msg-passive_mode"); 
  }
  
  $sent_file = CAppUI::conf('root_dir')."/offline.php";
  $remote_file = $ftp->fileprefix . "test.txt";

  $ftp->sendFile($sent_file, $remote_file);
  CAppUI::stepAjax("CFTP-success-transfer_out", E_USER_NOTICE, $sent_file, $remote_file);

  $get_file = "tmp/offline.php";
  $ftp->getFile($remote_file, $get_file);
  CAppUI::stepAjax("CFTP-success-transfer_in", E_USER_NOTICE, $remote_file, $get_file);
  
  $ftp->delFile($remote_file);
  CAppUI::stepAjax("CFTP-success-deletion", E_USER_NOTICE, $remote_file);
} 
catch (CMbException $e) {
  $e->stepAjax();
}

