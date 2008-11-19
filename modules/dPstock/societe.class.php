<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSociete extends CMbObject {
  // DB Table key
  var $societe_id     = null;

  // DB Fields
  var $name            = null;
  var $address         = null;
  var $postal_code     = null;
  var $city            = null;
  var $phone           = null;
  var $fax             = null;
  var $siret           = null;
  var $email           = null;
  var $contact_name    = null;
  var $contact_surname = null;

  // Object References
  //     Multiple
  var $_ref_product_references = null;
  var $_ref_products   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'societe';
    $spec->key   = 'societe_id';
    return $spec;
  }

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["products"]           = "CProduct societe_id";
	  $backRefs["product_orders"]     = "CProductOrder societe_id";
	  $backRefs["product_references"] = "CProductReference societe_id";
	  return $backRefs;
	}

	function getSpecs() {
    $specs = parent::getSpecs();
    $specs['name']            = 'notNull str maxLength|50';
    $specs['address']         = 'str';
    $specs['postal_code']     = 'numchar minLength|4 maxLength|5';
    $specs['city']            = 'str';
    $specs['phone']           = 'numchar length|10 mask|99S99S99S99S99';
    $specs['fax']             = 'numchar length|10 mask|99S99S99S99S99';
    $specs['siret']           = 'code siret';
    $specs['email']           = 'email';
    $specs['contact_name']    ='str maxLength|50';
    $specs['contact_surname'] = 'str maxLength|50';
    return $specs;
  }

  function getSeeks() {
    return array (
      'name'            => 'like',
      'city'            => 'like',
      'contact_name'    => 'like',
      'contact_surname' => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  function loadRefsBack() {
    $this->_ref_product_references = $this->loadBackRefs('product_references');
    $this->_ref_products = $this->loadBackRefs('products');
  }

}
?>