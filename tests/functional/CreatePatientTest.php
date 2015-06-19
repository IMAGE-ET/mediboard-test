<?php
/**
 * CreatePatientTest
 * @description Test creation of a patient
 * @screen      DossierPatientPage
 *
 * @package     Mediboard
 * @subpackage  Tests
 * @author      SARL OpenXtrem <dev@openxtrem.com>
 * @license     GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version     SVN: $Id: CreatePatientTest.php $
 * @link        http://www.mediboard.org
 */

require_once __DIR__ . "/SeleniumTestCase.php";
require_once __DIR__."/pages/HomePage.php";
require_once __DIR__."/pages/LoginPage.php";

class CreatePatientTest extends SeleniumTestCase {

  /** @var $dpPage DossierPatientPage */
  public $dpPage = null;
  public static $endOfClass = false;

  public $patientFirstname = "PatientFirstname";
  public $patientLastname = "PatientLastname";
  public $patientGender = "m";
  public $patientBirthDate = "12/12/1999";

  public function testCreatePatientOk() {
    $this->dpPage = new DossierPatientPage($this);
    $this->dpPage->searchPatientByName("notYetAPatient");
    $this->dpPage->createPatient($this->patientFirstname,$this->patientLastname,$this->patientGender,$this->patientBirthDate);
    $this->dpPage->searchPatientByName($this->patientLastname);

    $this->assertEquals(strtoupper($this->patientLastname),
      $this->byCssSelectorAndWait("#vwPatient > table:nth-child(1) > tbody:nth-child(1) > tr:nth-child(3) > td:nth-child(3)")->text());
    $this->screenshot($this->getTestId()."_".$this->getBrowser().".jpg");

    self::$endOfClass = true;
  }

  public function tearDown() {
    if (self::$endOfClass) {
      $this->dpPage->goToDossierPatient();
      $this->dpPage->searchPatientByName($this->patientLastname);

      $this->dpPage->purgePatient();
      $this->closeWindow();
      self::$endOfClass = false;
    }
  }
}