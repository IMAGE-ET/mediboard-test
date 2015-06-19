<?php
/**
 * Login page representation
 *
 * @package    Tests
 * @subpackage Pages
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: LoginPage.php $
 * @link       http://www.mediboard.org
 */
require_once "HomePage.php";

class LoginPage {

  /** @var  SeleniumTestCase $driver */
  public $driver;

  function __construct($driver) {
    $this->driver = $driver;
    $this->driver->url("/");
  }

  /**
   * Fill the login form field
   *
   * @param string $login User login
   */
  function setLogin($login) {
    $loginEdit = $this->driver->getFormField("loginFrm","username");
    $loginEdit->value($login);
  }

  /**
   * Fill the password form field
   *
   * @param string $passwd User password
   */
  function setPasswd($passwd) {
    $passwordEdit = $this->driver->getFormField("loginFrm","password");
    $passwordEdit->value($passwd);
  }

  /**
   * Perform a click on the login button
   */
  function clickLoginButton() {
    $loginButton = $this->driver->byCssSelector(".button > button:nth-child(1)");
    $loginButton->click();
  }

  /**
   * Perform the login action with the login et password params
   *
   * @param string $login   User login
   * @param string $passwd  User password
   *
   * @return ConsultationsPage
   */
  function doLogin($login, $passwd) {
    $this->setLogin($login);
    $this->setPasswd($passwd);
    $this->clickLoginButton();
    if(!isset($login) || !isset($passwd))
      $this->driver->acceptAlert();
    return new HomePage($this->driver);
  }

}