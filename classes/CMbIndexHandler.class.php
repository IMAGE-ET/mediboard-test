<?php /* $Id: CMbObjectHandler.class.php 12920 2011-08-23 10:34:22Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12920 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CMbIndexHandler 
 * @abstract Event handler class for Mediboard index main dispatcher
 */
 
abstract class CMbIndexHandler {
  function onBeforeMain() {}

  function onAfterMain() {}
}

?>