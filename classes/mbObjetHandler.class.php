<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CMbObjectHandler 
 * @abstract Event handler class for CMbObject
 */
 
abstract class CMbObjectHandler {
  
  function onStore(CMbObject &$mbObject) {}
  
  function onMerge(CMbObject &$mbObject) {}
  
  function onDelete(CMbObject &$mbObject) {}
}

?>