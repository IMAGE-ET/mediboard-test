<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Event handler class for Mediboard index main dispatcher
 */
abstract class CMbIndexHandler {
  /**
   * User authentication event
   *
   * @throws CUserAuthenticationFailure
   * @throws CUserAuthenticationSuccess
   *
   * @return void
   */
  function onUserAuthentication() {

  }

  /**
   * Before main.php inclusion
   *
   * @return void
   */
  function onBeforeMain() {

  }

  /**
   * After main.php inclusion
   *
   * @return void
   */
  function onAfterMain() {

  }
}
