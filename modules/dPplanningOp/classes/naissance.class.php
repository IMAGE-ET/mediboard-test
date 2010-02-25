<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/


/**
 * Table SJ00
 * SJTYPE = 
 * '' hors maternité
 * 'MR' Mère
 * 'nn' Rang du bébé
 */
class CNaissance extends CMbObject {
  // DB Table key
  var $naissance_id = null;

  // DB References
  var $operation_id = null;

  // DB Fields
  var $nom_enfant      = null;
  var $prenom_enfant   = null;
  var $date_prevue     = null;
  var $date_reelle     = null;
  var $debut_grossesse = null;
      
  // DB References
  var $_ref_operation = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'naissance';
    $spec->key   = 'naissance_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["operation_id"]    = "ref notNull class|COperation";
    $specs["nom_enfant"]      = "str notNull confidential";
    $specs["prenom_enfant"]   = "str";
    $specs["date_prevue"]     = "date";
    $specs["date_reelle"]     = "dateTime";
    $specs["debut_grossesse"] = "date";
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_operation = new COperation;
    $this->_ref_operation->load($this->operation_id);
  }
}
?>