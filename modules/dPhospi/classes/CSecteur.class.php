<?php

/**
 * dPhospi
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSecteur extends CMbObject {
  // DB Table key
  var $secteur_id = null; 
  
  // DB references
  var $group_id       = null;

  // DB Fields
  var $nom         = null;
  var $description = null;
  
   // Object references
  var $_ref_services = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'secteur';
    $spec->key   = 'secteur_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["services"] = "CService secteur_id";
    
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["nom"]         = "str notNull";
    $props["description"] = "text seekable";
    
    return $props;
  }
  
  function loadRefsServices() {
    return $this->_ref_services = $this->loadBackRefs("services");
  }
  
}