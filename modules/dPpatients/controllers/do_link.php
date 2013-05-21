<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$objects_id     = CValue::post('objects_id');
if (!is_array($objects_id)) {
  $objects_id = explode("-", $objects_id);
}

if (count($objects_id) != 2) {
  CAppUI::stepAjax("Trop d'objet pour réaliser une association", UI_MSG_ERROR);
}

$objects = array();

if (class_exists("CPatient") && count($objects_id)) {  
  $patient      = new CPatient();
  $patient_link = new CPatient();

  if (!$patient->load($objects_id[0]) || !$patient_link->load($objects_id[1])) {
    CAppUI::stepAjax("Chargement impossible du patient", UI_MSG_ERROR);
  }

  $patient->patient_link_id = $patient_link->_id;
  if ($msg = $patient->store()) {
    CAppUI::stepAjax("Association du patient impossible : $msg", UI_MSG_ERROR);
  }

  CAppUI::stepAjax("$patient->_view associé avec $patient_link->_view");
}

CApp::rip();
