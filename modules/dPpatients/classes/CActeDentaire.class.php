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
  var $rank               = null;
  var $commentaire         = null;
  var $ICR                 = null;
  var $consult_id          = null;
  
  // Ref fields 
  var $_ref_consultation   = null;
  
  // Ext fields
  var $_ref_code_ccam      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_dentaire';
    $spec->key   = 'acte_dentaire_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["devenir_dentaire_id"] = "ref notNull class|CDevenirDentaire cascade";
    $specs["code"]                = "str notNull";
    $specs["rank"]                = "num pos notNull";
    $specs["commentaire"]         = "text helped";
    $specs["consult_id"]          = "ref class|CConsultation";
    $specs["ICR"]                 = "num pos";
    return $specs;
  }

  static function searchICR($code) {
    $ds = CSQLDataSource::get("ccamV2");
    $query = $ds->prepare("SELECT * FROM ccam_ICR WHERE code = %", $code);
    $result = $ds->exec($query);
    if($ds->numRows($result)) {
      $row = $ds->fetchArray($result);
      return $row['ICR'];
    }
  }
  
  function delete() {
    $this->completeField("devenir_dentaire_id", "rank");
    $devenir_dentaire = $this->loadFwdRef("devenir_dentaire_id");
    $actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();
    foreach ($actes_dentaires as &$_acte_dentaire) {
      if ($_acte_dentaire->_id == $this->_id) continue;
      if ($_acte_dentaire->rank > $this->rank) {
        $_acte_dentaire->rank --;
        if ($msg = $_acte_dentaire->store()) {
          CAppUI::setMsg($msg);
        }
      }
    }
    parent::delete();
  }
  
  function loadRefCodeCCAM() {
    return $this->_ref_code_ccam = CCodeCCAM::get($this->code);  
  }
  
  function loadRefConsultation() {
    return $this->_ref_consultation = $this->loadFwdRef("consult_id");
  }
}
?>