<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Acte dentaire
 */
class CActeDentaire extends CMbObject {
  // DB Table key
  public $acte_dentaire_id;

  // DB Fields
  public $devenir_dentaire_id;
  public $code;
  public $rank;
  public $commentaire;
  public $ICR;
  public $consult_id;
  
  // Ref fields 
  public $_ref_consultation;
  
  // Ext fields
  public $_ref_code_ccam;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_dentaire';
    $spec->key   = 'acte_dentaire_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
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

  /**
   * Search an ICR by it's code
   *
   * @param string $code The code to find
   *
   * @return mixed|null
   */
  static function searchICR($code) {
    $ds = CSQLDataSource::get("ccamV2");
    $query = $ds->prepare("SELECT * FROM ccam_ICR WHERE code = %", $code);
    $result = $ds->exec($query);
    if ($ds->numRows($result)) {
      $row = $ds->fetchArray($result);
      return $row['ICR'];
    }

    return null;
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    $this->completeField("devenir_dentaire_id", "rank");

    /** @var CDevenirDentaire $devenir_dentaire */
    $devenir_dentaire = $this->loadFwdRef("devenir_dentaire_id");
    $actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();

    foreach ($actes_dentaires as &$_acte_dentaire) {
      if ($_acte_dentaire->_id == $this->_id) {
        continue;
      }

      if ($_acte_dentaire->rank > $this->rank) {
        $_acte_dentaire->rank --;
        if ($msg = $_acte_dentaire->store()) {
          CAppUI::setMsg($msg);
        }
      }
    }
    parent::delete();
  }

  /**
   * Load the CCAM code object
   *
   * @return CCodeCCAM
   */
  function loadRefCodeCCAM() {
    return $this->_ref_code_ccam = CCodeCCAM::get($this->code);  
  }

  /**
   * Load the consultation
   *
   * @return CConsultation
   */
  function loadRefConsultation() {
    return $this->_ref_consultation = $this->loadFwdRef("consult_id");
  }
}
