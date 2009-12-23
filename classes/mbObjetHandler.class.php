<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CMbObjectHandler 
 * @abstract Event handler class for CMbObject
 */
 
abstract class CMbObjectHandler {
  function onAfterStore(CMbObject &$mbObject) {}
  
  function onAfterMerge(CMbObject &$mbObject) {}
  
  function onAfterDelete(CMbObject &$mbObject) {}
  
  function onBeforeStore(CMbObject &$mbObject) {}
  
  function onBeforeMerge(CMbObject &$mbObject) {}
  
  function onBeforeDelete(CMbObject &$mbObject) {}
}

?>