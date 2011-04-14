<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CConstanteItem extends CMbObject {
    // DB Table key
  var $constante_item_id = null;
  
  // DB Fields
  var $category_prescription_id = null;
  var $field_constante = null;
  var $commentaire = null;
  
  // Form Fields
  var $_ref_category_prescription = null;
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'constante_item';
    $spec->key   = 'constante_item_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["category_prescription_id"] = "ref notNull class|CCategoryPrescription";
    $specs["field_constante"] = "enum list|".implode("|", array_keys(CConstantesMedicales::$list_constantes)) . " seekable";
    $specs["commentaire"] = "str";
    return $specs;
  }

  function loadRefCategoryPrescription() {
    $category_prescription = new CCategoryPrescription;
    return $this->_ref_category_prescription = $category_prescription->load($this->category_prescription_id);
  }
}