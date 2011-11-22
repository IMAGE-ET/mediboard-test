<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

include_once('ajax_connexion_mllp.php');

$exchange_source->setData("Hello world !\n");

try {
  $exchange_source->send();
  CAppUI::stepAjax("Données transmises au serveur MLLP");
  if ($ack = $exchange_source->getData()) {
    CAppUI::stepAjax($ack);
  }
} catch (Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
} 

?>