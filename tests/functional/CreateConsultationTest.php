<?php
/**
 * CreateConsultationtest
 * @description Test creation of a consultation
 * @screen      DossierPatientPage, ConsultationPage
 *
 * @package     Mediboard
 * @subpackage  Tests
 * @author      SARL OpenXtrem <dev@openxtrem.com>
 * @license     GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version     SVN: $Id: CreateConsultationTest.php $
 * @link        http://www.mediboard.org
 *
 */

require_once __DIR__ . "/SeleniumTestCase.php";
require_once __DIR__."/pages/HomePage.php";
require_once __DIR__."/pages/LoginPage.php";

class CreateConsultationTest extends SeleniumTestCase {


  /** @var DossierPatientPage $dpPage */
  public $dpPage = null;
  public static $endOfClass = false;

  public $patientFirstname = "PatientFirstname";
  public $patientLastname = "PatientLastname";
  public $patientGender = "m";
  public $patientBirthDate = "12/12/1999";

  public $chir_id = "733";
  public $datePlage = "2015-07-01";

  public function testCreateConsultationOk() {
    $consultPage = new ConsultationsPage($this);
    $consultPage->createPlageConsultation($this->chir_id, $this->datePlage);
    $this->dpPage = $consultPage->goToDossierPatient();
    $this->dpPage->searchPatientByName("notYetAPatient");
    $this->dpPage->createPatient($this->patientFirstname,$this->patientLastname,$this->patientGender,$this->patientBirthDate);
    $consultPage = $this->dpPage->goToConsultations();
    $consultPage->createConsultation($this->chir_id, $this->datePlage, $this->patientLastname);
    $this->screenshot($this->getTestId()."_".$this->getBrowser().".jpg");
  }


  public function testCreateConsultationImmediateOk() {
    $this->dpPage = new DossierPatientPage($this);
    $this->dpPage->searchPatientByName("notYetAPatient");
    $this->dpPage->createPatient($this->patientFirstname, $this->patientLastname, $this->patientGender, $this->patientBirthDate);
    $this->dpPage->searchPatientByName($this->patientLastname);
    $this->dpPage->createConsultationImmediate($this->chir_id);
    $this->screenshot($this->getTestId()."_".$this->getBrowser().".jpg");

    self::$endOfClass = true;
  }


  public function tearDown() {
    $this->dpPage->goToDossierPatient();
    $this->dpPage->searchPatientByName($this->patientLastname);
    $this->dpPage->purgePatient();
    if(self::$endOfClass) {
      $consultPage = $this->dpPage->goToConsultations();
      $consultPage->removePlageConsultationAutomatique($this->chir_id);
      $consultPage->removePlageConsultation($this->chir_id, $this->datePlage);
      self::$endOfClass = false;
    }
  }
}