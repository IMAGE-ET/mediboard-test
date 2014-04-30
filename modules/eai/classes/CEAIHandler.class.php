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
 * Class CEAIHandler
 *
 * @abstract Event handler class for Mediboard
 */
abstract class CEAIHandler {
  static $handlers;

  /**
   * Staticly build object handlers array
   *
   * @return void
   */
  protected static final function makeHandlers() {
    if (is_array(self::$handlers)) {
      return;
    }

    // Static initialisations
    self::$handlers = array();
    foreach (CAppUI::conf("eai_handlers") as $_class => $_active) {
      if ($_active && !isset(self::$ignoredHandlers[$_class])) {
        if (!class_exists($_class)) {
          trigger_error("Model object handler missing class '$_class'", E_USER_ERROR);
          continue;
        }

        self::$handlers[$_class] = new $_class;
      }
    }
  }

  /**
   * Subject notification mechanism
   *
   * @param string $message on[Before|After][Build]
   *
   * @return void
   */
  static function notify($message/*, ... */) {
    $args = func_get_args();
    array_shift($args); // $message

    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $_handler) {
      try {
        call_user_func_array(array($_handler, "on$message"), $args);
      }
      catch (Exception $e) {
        CAppUI::setMsg($e, UI_MSG_ERROR);
      }
    }
  }

  /**
   * Trigger before build message
   *
   * @return bool
   */
  function onBeforeBuild() {
  }

  /**
   * Trigger after build message
   *
   * @return bool
   */
  function onAfterBuild() {
  }
}
