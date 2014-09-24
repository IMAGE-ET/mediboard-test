<?php 

/**
 * $Id$
 *  
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$action = CValue::get("action");
$state  = CValue::get("state");

switch ($action) {
  case "verifyStatus":
    $result = CPatientStateTools::verifyStatus();
    CAppUI::stepAjax("Il y a $result patients n'ayant pas de statut");
    break;
  case "createStatus":
    $result = CPatientStateTools::createStatus($state);
    CAppUI::stepAjax("Il y a $result patients dont le status a été créés");
    break;
  default:
    CAppUI::stepAjax("Action non spécifiée");
}

CAppUI::getMsg();
