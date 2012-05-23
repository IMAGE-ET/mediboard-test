<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12920 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CMbTemplatePlaceholder 
 * @abstract Template placeholder class for module extensibility of main style templates
 */
 
abstract class CMbTemplatePlaceholder {
  var $module = null;
  var $minitoolbar = null;
  
  function __construct($module) {
    $this->module = $module;
  }
}

?>