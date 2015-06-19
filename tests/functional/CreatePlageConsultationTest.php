<?php
/**
 * CreatePlageConsultationtest
 * @description Test creation of a "plage de consultation"
 * @screen      ConsultationPage
 *
 * @package     Mediboard
 * @subpackage  Tests
 * @author      SARL OpenXtrem <dev@openxtrem.com>
 * @license     GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version     SVN: $Id: CreatePlageConsultationTest.php $
 * @link        http://www.mediboard.org
 *
 */

require_once __DIR__ . "/SeleniumTestCase.php";
require_once __DIR__."/pages/HomePage.php";
require_once __DIR__."/pages/ConsultationsPage.php";
require_once __DIR__."/CsvFileIterator.php";

class CreatePlageConsultationTest extends SeleniumTestCase {


  /** @var ConsultationsPage $dpPage */
  public $consultationPage = null;

  public static $endOfClass = false;

  public $chir_id = "733";
  public $datePlage = "2015-07-01";


  public function testCreatePlageConsultation() {
    $this->consultationPage = new ConsultationsPage($this);
    $this->consultationPage->createPlageConsultation($this->chir_id,$this->datePlage);
    $this->screenshot($this->getTestId()."_".$this->getBrowser().".jpg");
  }


  public function tearDown() {
    $this->consultationPage->removePlageConsultation($this->chir_id, $this->datePlage);
  }
}