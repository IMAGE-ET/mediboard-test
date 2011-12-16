<?php /* $Id: CIdSante400.class.php 13724 2011-11-09 15:10:29Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 13724 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMovement extends CMbMetaObject {
  // DB Table key
  var $movement_id      = null;
  
  // DB fields
  var $movement_type         = null;
  var $original_trigger_code = null;
  var $last_update           = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'movement';
    $spec->key   = 'movement_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["movement_type"]         = "enum notNull list|PADM|ADMI|MUTA|SATT|SORT";
    $props["original_trigger_code"] = "str length|3";
    $props["last_update"]           = "dateTime notNull";
    
    return $props;
  }
  
  function store() {
    // Création idex sur le mouvement (movement_type + original_trigger_code + object_guid + tag (mvt_id))
    
    $this->last_update = mbDateTime();
    
    return parent::store();
  }
}