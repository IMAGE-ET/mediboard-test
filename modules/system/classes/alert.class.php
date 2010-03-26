<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CAlert Class
 */
class CAlert extends CMbMetaObject {
  // DB Table key
  var $alert_id = null;
  
  // DB Fields
  var $tag      = null;
  var $level    = null;
  var $comments = null;
  var $handled  = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'alert';
    $spec->key   = 'alert_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["tag"]      = "str notNull";
    $specs["level"]    = "enum list|low|medium|high default|medium notNull";
    $specs["comments"] = "text";
    $specs["handled"]  = "bool notNull default|0";
    return $specs;
  }
}

?>