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
 * Class CMbIndexHandler 
 * @abstract Event handler class for Mediboard index main dispatcher
 */
abstract class CMbIndexHandler {
  
  function onBeforeMain() {}

  function onAfterMain() {}
}
