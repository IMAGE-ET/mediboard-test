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
class CPlateauTechnique extends CMbObject {
  // DB Table key
  var $plateau_id = null;
  
  // References
  var $group_id = null;

  // DB Fields
  var $nom      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plateau_technique';
    $spec->key   = 'plateau_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]      = "str notNull";
    return $specs;
  }
}

?>