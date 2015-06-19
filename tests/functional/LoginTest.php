<?php
/**
 * LoginTest
 * @description Try to connect to the app
 * @screen      LoginPage
 *
 * @package     Mediboard
 * @subpackage  Tests
 * @author      SARL OpenXtrem <dev@openxtrem.com>
 * @license     GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version     SVN: $Id: LoginTest.php $
 * @link        http://www.mediboard.org
 */

require_once __DIR__ . "/SeleniumTestCase.php";
require_once __DIR__."/pages/HomePage.php";
require_once __DIR__."/pages/LoginPage.php";
require_once __DIR__."/CsvFileIterator.php";

class LoginTest extends SeleniumTestCase {

  /**
   * @dataProvider credentialProvider
   */
  public function testLogin($login, $password, $expected) {
    $loginPage = new LoginPage($this);
    $homePage = $loginPage->doLogin($login,$password);
    //TODO switch
    if ($expected == "pass") {
      $this->assertEquals(strtoupper($login),$this->byCssSelectorAndWait(".welcome")->text());
      $homePage->doLogOut();
    }
    elseif ($expected == "fail") {
      if ($login == "" || $password == "") {
        $this->acceptAlert();
      }
      $this->assertEquals("LOCALHOST ? Connexion",utf8_decode($this->title()));
    }
  }

  /**
   * Provide login password informations
   * format login,password,pass|fail
   * see /test/data/login.csv for more details
   *
   * @return array
   */
  public function credentialProvider() {
    return new CsvFileIterator("login.csv");
  }

}