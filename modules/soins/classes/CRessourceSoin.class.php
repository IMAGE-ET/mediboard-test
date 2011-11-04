<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CRessourceSoin extends CMbObject {
  
  // DB Table key
  var $ressource_soin_id  = null;
  
  // DB Fields
  var $libelle = null;
  var $cout = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ressource_soin';
    $spec->key   = 'ressource_soin_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["libelle"] = "text notNull";
    $specs["cout"]    = "float";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["indices_couts"]   = "CIndiceCout ressource_soin_id";
    
    return $backProps;
  }
}

?>