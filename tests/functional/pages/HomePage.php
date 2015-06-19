<?php
/**
 * Home page representation, abstract class which defines header and navbar
 *
 * @package    Tests
 * @subpackage Pages
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: HomePage.php $
 * @link       http://www.mediboard.org
 */

require_once __DIR__ . "/CimPage.php";
require_once __DIR__ . "/BlocPage.php";
require_once __DIR__ . "/CcamPage.php";
require_once __DIR__ . "/ConsultationsPage.php";
require_once __DIR__ . "/DossierPatientPage.php";
require_once __DIR__ . "/ModelesPage.php";
require_once __DIR__ . "/PlanifSejourPage.php";
require_once __DIR__ . "/LoginPage.php";

class HomePage {

  /** @var  SeleniumTestCase $driver */
  public $driver;

  protected $module_name;
  protected $tab_name;

  function __construct($driver) {
    $this->driver = $driver;
    if ($this->module_name) {
      $this->driver->url("/?login=selenium:test");
      $this->driver->url("/index.php?m=$this->module_name".($this->tab_name ? "&tab=$this->tab_name" : ""));
    }
  }

  function goToCim() {
    return new CimPage($this->driver);
  }

  function goToCcam() {
    return new CcamPage($this->driver);
  }

  function goToDossierPatient() {
    return new DossierPatientPage($this->driver);
  }

  function goToConsultations() {
    return new ConsultationsPage($this->driver);
  }

  function goToModeles() {
    return new ModelesPage($this->driver);
  }

  function goToBloc() {
    return new BlocPage($this->driver);
  }

  function goToPlanifSejour() {
    return new PlanifSejourPage($this->driver);
  }

  public function getTitle() {
    return utf8_decode($this->driver->byCssSelectorAndWait("div.title:nth-child(3) > h1:nth-child(2)")->text());
  }

  function doLogOut() {
    $this->driver->byCssSelectorAndWait(".menu > a:nth-child(7)")->click();
  }

  /**
   * Global patient selector
   * Search for a patient and select it if found
   *
   * @param string $lastname  Patient lastname
   * @param string $firstname Patient firstname
   */
  function patientSelector($lastname, $firstname=null) {
    $driver = $this->driver;

    $driver->changeFrameFocus("dPpatients","pat_selector");
    $lastnameField = $driver->getFormField("patientSearch","name");
    $firstnameField = $driver->getFormField("patientSearch","name");

    $lastnameField->clear();
    $firstnameField->clear();

    $lastnameField->value($lastname);
    $firstnameField->value($firstname);
    $driver->byId("pat_selector_search_pat_button")->click();
    $driver->byIdAndWait("inc_pat_selector_select_pat")->click();
  }

}