<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CActeDentaire extends CMbObject {
  // DB Table key
  var $acte_dentaire_id    = null;

  // DB Fields
  var $devenir_dentaire_id = null;
  var $code                = null;
  var $commentaire         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_dentaire';
    $spec->key   = 'acte_dentaire_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["devenir_dentaire_id"]  = "ref notNull class|CDevenirDentaire";
    $specs["code"]                 = "str notNull";
    $specs["commentaire"]          = "text helped";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_dentaires"] = "CActeDentaire devenir_dentaire_id";
    return $backProps;
  }
  
  function loadRefsActesDentaires() {
    return $this->_ref_actes_dentaires = $this->loadBackRefs("actes_dentaires");
  }
}
?>