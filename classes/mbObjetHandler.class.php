<?php /* $Id: mbobject.class.php 2252 2007-07-12 10:00:15Z rhum1 $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
*/

/**
 * Class CMbObjectHandler 
 * @abstract Event handler class for CMbObject
 */
 
abstract class CMbObjectHandler {
  
  function onStore(CMbObject &$mbObject) {}
  
  function onDelete(CMbObject &$mbObject) {}
}

?>