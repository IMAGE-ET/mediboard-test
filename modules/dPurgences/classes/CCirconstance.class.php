<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CCirconstance extends CMbObject {
  // DB Table key
  var $circonstance_id     = null;
  
  // DB Fields
  var $code            = null;
  var $libelle         = null;
  var $commentaire     = null; 
 
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'circonstance';
    $spec->key   = 'circonstance_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["code"]    = "str notNull";
    $specs["libelle"] = "str notNull seekable";
    $specs["commentaire"]   = "text notNull seekable";
    
    return $specs;
  }
}

?>