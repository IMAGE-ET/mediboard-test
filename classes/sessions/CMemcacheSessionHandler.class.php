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
 * Memcache based Session Handler
 */
class CMemcacheSessionHandler implements ISessionHandler {
  /**
   * @see parent::init()
   */
  function init() {
    return ini_set("session.save_handler", "memcache");
  }

  /**
   * @see parent::useUserHandler()
   */
  function useUserHandler() {
    return false;
  }

  /**
   * @see parent::open()
   */
  function open() {
    return false;
  }

  /**
   * @see parent::close()
   */
  function close() {
    return false;
  }

  /**
   * @see parent::read()
   */
  function read($session_id) {
    return false;
  }

  /**
   * @see parent::write()
   */
  function write($id, $data) {
    return false;
  }

  /**
   * @see parent::destroy()
   */
  function destroy($id) {
    return false;
  }

  /**
   * @see parent::gc()
   */
  function gc($max) {
    return false;
  }

  /**
   * @see parent::listSessions()
   */
  function listSessions() {
    return array();
  }

  /**
   * @see parent::setLifeTime()
   */
  function setLifeTime($lifetime) {
  }
}