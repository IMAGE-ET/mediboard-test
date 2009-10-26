<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
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

	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["products"]           = "CProduct societe_id";
	  $backProps["product_orders"]     = "CProductOrder societe_id";
	  $backProps["product_references"] = "CProductReference societe_id";
	  return $backProps;
	}

	function getProps() {
    $specs = parent::getProps();
    $specs['name']            = 'str notNull maxLength|50 seekable';
    $specs['address']         = 'str';
    $specs['postal_code']     = 'numchar minLength|4 maxLength|5';
    $specs['city']            = 'str seekable';
    $specs['phone']           = 'numchar length|10 mask|'.str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    $specs['fax']             = 'numchar length|10 mask|'.str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    $specs['siret']           = 'code siret';
    $specs['email']           = 'email';
    $specs['contact_name']    = 'str maxLength|50 seekable';
    $specs['contact_surname'] = 'str maxLength|50 seekable';
    return $specs;
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