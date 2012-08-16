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

class CRessourceMaterielle extends CMbObject {
  // DB Table Key
  var $ressource_materielle_id = null;
  
  // DB References
  var $type_ressource_id    = null;
  var $group_id             = null;
  
  // DB Fields
  var $libelle              = null;
  var $deb_activite         = null;
  var $fin_activite         = null;
  var $retablissement       = null;
  
  // Ref Fields
  var $_ref_type_ressource  = null;
  var $_ref_usages          = null;
  var $_ref_indispos        = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ressource_materielle';
    $spec->key   = 'ressource_materielle_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs['group_id']          = "ref class|CGroups notNull";
    $specs["type_ressource_id"] = "ref class|CTypeRessource notNull autocomplete|libelle";
    $specs["libelle"]           = "str notNull seekable";
    $specs["deb_activite"]      = "date";
    $specs["fin_activite"]      = "date";
    $specs["retablissement"]    = "bool default|0";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["indispos"] = "CIndispoRessource ressource_materielle_id";
    $backProps["usages"]   = "CUsageRessource ressource_materielle_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle;
  }
  
  function loadRefTypeRessource() {
    return $this->_ref_type_ressource = $this->loadFwdRef("type_ressource_id", true);
  }
  
  function loadRefsUsagesDateTime($datetime = null) {
    if ($datetime) {
      $usage = new CUsageRessource;
      $where = array();
      $ljoin = array();
      
      $ljoin["besoin_ressource"] = "usage_ressource.besoin_ressource_id = besoin_ressource.besoin_ressource_id";
      $ljoin["operations"] = "operations.operation_id = besoin_ressource.operation_id";
      $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
      
      $where[] = "(operations.date = '".mbDate($datetime)."' AND operations.plageop_id IS NULL) OR ".
                 "(operations.plageop_id IS NOT NULL AND plagesop.date = '".mbDate($datetime)."')";
      $where["usage_ressource.ressource_materielle_id"] = " = '$this->_id'";
      
      $usages = $usage->loadList($where, null, null, null, $ljoin);
      
      foreach ($usages as $key => $_usage) {
        $operation = $_usage->loadRefBesoin()->loadRefOperation();
        $operation->loadRefPlageOp();
        if ($datetime < $operation->_datetime || $datetime > $operation->_datetime) {
          unset($usages[$key]);
        }
      }
      
      return $this->_ref_usages = $usages;
    }
    
    return $this->_ref_usages = $this->loadBackRefs("usages");
  }
  
  function loadRefsUsages($from = null, $to = null) {
    if ($from && $to) { 
      $usage = new CUsageRessource;
      $where = array();
      $ljoin = array();
      
      $ljoin["besoin_ressource"] = "usage.ressource.besoin_ressource_id = besoin_ressource.besoin_ressource_id";
      $ljoin["operation"] = "operation.operation_id = besoin_ressource.operation_id";
      
      $where["operation.date"] = "BETWEEN '$from' AND '$to'";
      
      return $this->_ref_usages = $usage->loadList($where, null, null, null, $ljoin);
    }
    
    return $this->_ref_usages = $this->loadBackRefs("usages");
  }
  
  function loadRefsIndispos($from = null, $to = null) {
    if ($from && $to) {
      $indispo = new CIndispoRessource;
      $where = array(
        "ressource_materielle_id" => "= '$this->_id'",
        "deb" => " <= '$to'",
        "fin" => " >= '$from'"
      );
      return $this->_ref_indispos = $indispo->loadList($where);
    }
    
    return $this->_ref_indispos = $this->loadBackRefs("indispos");
  }
}
