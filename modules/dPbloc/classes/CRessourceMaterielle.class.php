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
  var $_ref_besoins         = null;
  
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
    $specs["retablissement"]    = "time";
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

  function loadRefsUsages($from = null, $to = null) {
    if ($from && $to) { 
      $usage = new CUsageRessource;
      $where = array();
      $ljoin = array();
      
      $ljoin["besoin_ressource"] = "usage_ressource.besoin_ressource_id = besoin_ressource.besoin_ressource_id";
      $ljoin["operations"] = "operations.operation_id = besoin_ressource.operation_id";
      $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
      $from_date = mbDate($from);
      $to_date   = mbDate($to);
      $where[] = "(operations.date BETWEEN '$from_date' AND '$to_date' AND operations.plageop_id IS NULL) ".
                 "OR (operations.plageop_id IS NOT NULL AND plagesop.date BETWEEN '$from_date' AND '$to_date')";
      
      // Sur les interventions non annulées
      $where[] = "operations.annulee = '0'";
      
      // Sur la ressource instanciée
      if ($this->_id) {
        $where["usage_ressource.ressource_materielle_id"] = " = '$this->_id'";
      }
      // Ou sur son type si nouvel objet
      else if ($this->type_ressource_id){
        $ljoin["ressource_materielle"] = "ressource_materielle.type_ressource_id = besoin_ressource.type_ressource_id";
        $where["ressource_materielle.type_ressource_id"] = "= '$this->type_ressource_id'";
      }
      
      $usages = $usage->loadList($where, null, null, null, $ljoin);
      $besoins = CMbObject::massLoadFwdRef($usages, "besoin_ressource_id");
      CMbObject::massLoadFwdRef($besoins, "operation_id");
      CMbObject::massLoadFwdRef($usages, "ressource_materielle_id");
      
      // Prendre en compte le temps de réhabilitation des ressources
      foreach ($usages as $key => $_usage) {
        $ressource = $_usage->loadRefRessource();
        $operation = $_usage->loadRefBesoin()->loadRefOperation();
        $operation->loadRefPlageOp();
        $deb_op = $operation->_datetime;
        $fin_op  = mbAddDateTime($operation->temp_operation, $deb_op);
        $fin_op_reha = mbAddDateTime($ressource->retablissement, $fin_op);
        if ($deb_op > $to || $fin_op_reha < $from) {
          unset($usages[$key]);
        }
      }
      
      return $this->_ref_usages = $usages;
    }
    
    return $this->_ref_usages = $this->loadBackRefs("usages");
  }
  
  function loadRefsIndispos($from = null, $to = null) {
    if ($from && $to) {
      $indispo = new CIndispoRessource;
      $where = array();
      $ljoin = array();
      
      $where["deb"] = " <= '$to'";
      $where["fin"] = " >= '$from'";
      
      // Sur la ressource instanciée
      if ($this->_id) {
        $where["ressource_materielle_id"] = "= '$this->_id'";
      }
      // Ou sur son type si nouvel objet
      elseif ($this->type_ressource_id) {
        $ljoin["ressource_materielle"] = "ressource_materielle.ressource_materielle_id = indispo_ressource.ressource_materielle_id";
        $where["ressource_materielle.type_ressource_id"] = "= '$this->type_ressource_id'";
      }
      return $this->_ref_indispos = $indispo->loadList($where, null, null, null, $ljoin);
    }
    
    return $this->_ref_indispos = $this->loadBackRefs("indispos");
  }
  
  function loadRefsBesoins($from, $to) {
    if (!$from && !$to) {
      return $this->_ref_besoins = array();
    }
    
    $besoin = new CBesoinRessource;
    $where = array();
    $ljoin = array();
    $from_date = mbDate($from);
    $to_date   = mbDate($to);
    
    $ljoin["operations"] = "besoin_ressource.operation_id = operations.operation_id";
    $ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
    $ljoin["usage_ressource"] = "usage_ressource.besoin_ressource_id = besoin_ressource.besoin_ressource_id";
    
    // On ne charge que les besoins qui n'ont pas d'usage 
    $where[] = "usage_ressource.usage_ressource_id IS NULL";
    
    $where[] = "(operations.date BETWEEN '$from_date' AND '$to_date' AND operations.plageop_id IS NULL) ".
                 "OR (operations.plageop_id IS NOT NULL AND plagesop.date BETWEEN '$from_date' AND '$to_date')";
    
    // Sur les interventions non annulées
    $where[] = "operations.annulee = '0'";
    
    if ($this->type_ressource_id) {
      $ljoin["ressource_materielle"] = "ressource_materielle.type_ressource_id = besoin_ressource.type_ressource_id";
      $where["ressource_materielle.type_ressource_id"] = "= '$this->type_ressource_id'";
    }
    
    $besoins = $besoin->loadList($where, null, null, null, $ljoin);
    CMbObject::massLoadFwdRef($besoins, "operation_id");
    
    foreach ($besoins as $key => $_besoin) {
      $operation = $_besoin->loadRefOperation();
      $operation->loadRefPlageOp();
      $deb_op = $operation->_datetime;
      $fin_op  = mbAddDateTime($operation->temp_operation, $deb_op);
      if ($deb_op > $to || $fin_op < $from) {
        unset($besoins[$key]);
      }
    }
    
    return $this->_ref_besoins = $besoins;
  }
}
