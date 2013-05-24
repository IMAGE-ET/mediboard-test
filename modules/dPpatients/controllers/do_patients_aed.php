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

class CDoPatientAddEdit extends CDoObjectAddEdit {
  function CDoPatientAddEdit() {
    $this->CDoObjectAddEdit("CPatient", "patient_id");

    if ($dialog = CValue::post("dialog")) {
      $this->redirectDelete .= $this->redirect."&a=pat_selector&dialog=1";
      $this->redirectStore  .= $this->redirect."&a=vw_edit_patients&dialog=1";
    }
    else {
      $tab = CValue::post("tab", "vw_edit_patients");
      $this->redirectDelete .= $this->redirect."&tab=$tab";
      $this->redirectStore  .= $this->redirect."&tab=$tab";
    }
  }

  function doStore() {
    parent::doStore();

    $dialog = CValue::post("dialog");

    if ($dialog) {
      $this->redirectStore .= "&a=pat_selector&dialog=1&name=".$this->_obj->nom.
        "&firstName=".$this->_obj->prenom.
        "&useVitale=".$this->_obj->_bind_vitale;
    }
    else {
      $this->redirectStore .= "m=dPpatients&tab=vw_idx_patients&id=".$this->_obj->patient_id."&nom=&prenom=";
    }
  }

  function doDelete() {
    parent::doDelete();

    $dialog = CValue::post("dialog");
    if ($dialog) {
      $this->redirectDelete .= "&name=".$this->_obj->nom."&firstName=".$this->_obj->prenom."&id=0";
    }
  }
}

$do = new CDoPatientAddEdit;
$do->doIt();
