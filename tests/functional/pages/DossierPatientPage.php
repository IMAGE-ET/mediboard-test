<?php
/**
 * DossierPatient page representation
 *
 * @package    Tests
 * @subpackage Pages
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: DossierPatientPage.php $
 * @link       http://www.mediboard.org
 */

require_once "HomePage.php";

class DossierPatientPage extends HomePage {

  protected $module_name = "dPpatients";

  /**
   * Search a patient by his name on the Dossier Patient page
   *
   * @param string $name Patient lastname
   */
  public function searchPatientByName($name) {
    $driver = $this->driver;

    $driver->window($driver->windowHandle());

    // Select the search input, clear it and set value
    $findNomField = $driver->byIdAndWait("find_nom");
    $findNomField->clear();
    $findNomField->value($name);
    // Click on research button
    $driver->byId("ins_list_patient_button_search")->click();
    $driver->waitForAjax("search_result_patient");
  }


  /**
   * Try to create a patient with the given params
   *
   * @param string $firstname   Patient firstname
   * @param string $lastname    Patient lastname
   * @param string $gender      Patient gender with format m|f
   * @param string $birthDate   Patient birth date with format dd/mm/yyyy
   */
  public function createPatient($firstname, $lastname, $gender, $birthDate) {
    $driver = $this->driver;

    // Avoid click on a disable button
    $buttonStyle = $this->driver->byIdAndWait("vw_idx_patient_button_create")->attribute("style");
    if($buttonStyle != "") {
      $driver->waitUntil(function() {
        if ($this->driver->byId("vw_idx_patient_button_create")->attribute("style") == "")
          return true;
        return null;
      }, 5000);
    }

    // Click on the create patient button
    $driver->byIdAndWait("vw_idx_patient_button_create")->click();

    // Change the focus on the modal
    $driver->changeFrameFocus('patients','vw_edit_patients');

    // Select, clear and set value of the lastname field
    $nameFormField = $driver->getFormField("editFrm","nom");
    $nameFormField->clear();
    $nameFormField->value($lastname);

    // Select, clear and set value of the firstname field
    $firstnameFormField = $driver->getFormField("editFrm","prenom");
    $firstnameFormField->clear();
    $firstnameFormField->value($firstname);

    // Set the gender
    if ($gender == "m") {
      $driver->byIdAndWait("labelFor_editFrm_sexe_m")->click();
    }
    elseif ($gender == "f") {
      $driver->byIdAndWait("labelFor_editFrm_sexe_f")->click();
    }

    // Select, click, clear and set value of birthdate field
    $birthDateFormField = $driver->getFormField("editFrm","naissance");
    $birthDateFormField->click();
    $birthDateFormField->clear();
    $driver->keys($birthDate);

    // Click on another field to trigger checkDoublon() event
    $nameFormField->click();
    $driver->waitForAjax("doublon-patient");

    // Submit the form
    $nameFormField->submit();

    // Change focus on current window
    $driver->window($driver->windowHandle());
    $driver->waitForAjax("search_result_patient");

    // Hack for chrome to get the focus on main window
    if ($driver->getBrowser() == "chrome") {
      $driver->refresh();
    }
  }


  /**
   * Try to create a "consultation immédiate"
   * vwPatient needs to be open
   *
   * @param string $praticien_id  Practitioner user_id
   */
  public function createConsultationImmediate($praticien_id) {
    $driver = $this->driver;

    $driver->waitForAjax("vwPatient");

    // Click on "Consultation immediate" button
    $driver->byIdAndWait("inc_vw_patient_button_consult_now")->click();
    // Select the pratitioner select html element
    $pratField = $driver->select($driver->getFormField("addConsultImmediate","_prat_id"));
    // Set the right option
    $pratField->selectOptionByValue($praticien_id);
    // Submit the form
    $pratField->submit();
    // Wait for loading
    $driver->waitForAjax("view-devis");
  }


  /**
   * Try to remove a patient using edit button
   * vwPatient needs to be open
   */
  public function removePatient() {
    $driver = $this->driver;

    $driver->waitForAjax("vwPatient");
    $driver->byCssSelectorAndWait(".edit")->click();
    $driver->waitForAjax("alert_tutelle");
    $driver->byCssSelectorAndWait(".trash")->click();
    $driver->acceptAlert();
    $driver->byCssSelectorAndWait("#patient_identite > tbody:nth-child(1) > tr:nth-child(3) > td:nth-child(2) > button:nth-child(2)");
  }


  /**
  * Try to purge a patient with JavaScript
   *
  */
  public function purgePatient() {
    $driver = $this->driver;

    $driver->waitForAjax("vwPatient");

    // Execute JavaScript to purge patient
    $driver->execute(array(
      'script' =>  "var patient_id = document.getElementsByName('patient_id')[0].value;
                    Patient.doPurge(patient_id)",
      'args' => array()
    ));
    // Wait for the end of purge
    $driver->waitForAjax("search_result_patient");
  }
}