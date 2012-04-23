<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CIndispoRessource extends CMbObject{
  // DB Table Key
  var $indispo_ressource_id = null;
  
  // DB References
  var $ressource_materielle_id    = null;
  
  // DB Fields
  var $deb                        = null;
  var $fin                        = null;
  var $commentaire                = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'indispo_ressource';
    $spec->key   = 'indispo_ressource_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["ressource_materielle_id"]    = "ref notNull class|CRessourceMaterielle";
    $specs["deb"]                        = "date notNull";
    $specs["fin"]                        = "date notNull";
    $specs["commentaire"]                = "text helped";
    
    return $specs;
  }
}
