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
  var $code            = null;
  var $distributor_code= null;
  var $customer_code   = null;
  var $manufacturer_code= null; // in the barcodes (http://www.morovia.com/education/symbology/scc-14.asp)
  var $address         = null;
  var $postal_code     = null;
  var $city            = null;
  var $phone           = null;
  var $fax             = null;
  var $siret           = null;
  var $email           = null;
  var $contact_name    = null;
  var $carriage_paid   = null;
  var $delivery_time   = null;
  var $departments     = null;
  
  var $_departments    = null;
  var $_is_supplier    = null;
  var $_is_manufacturer= null;

  // Object References
  //     Multiple
  var $_ref_product_references = null;
  var $_ref_product_orders = null;
  var $_ref_products   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'societe';
    $spec->key   = 'societe_id';
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["products"]           = "CProduct societe_id";
    $backProps["product_orders"]     = "CProductOrder societe_id";
    $backProps["product_references"] = "CProductReference societe_id";
    $backProps["product_receptions"] = "CProductReception societe_id";
    $backProps["articles_cahpp"]     = "CCAHPPArticle fournisseur_id";
    $backProps["receptions_bills"]   = "CProductReceptionBill societe_id";
    return $backProps;
  }

  function getProps() {
    $phone_mask = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    $specs = parent::getProps();
    $specs['name']            = 'str notNull maxLength|50 seekable show|0';
    $specs['code']            = 'str maxLength|80 seekable protected';
    $specs['distributor_code']= 'str maxLength|80 seekable protected';
    $specs['customer_code']   = 'str maxLength|80';
    $specs['manufacturer_code']= 'numchar length|5 seekable protected';
    $specs['address']         = 'text seekable';
    $specs['postal_code']     = 'str minLength|4 maxLength|5 seekable';
    $specs['city']            = 'str seekable';
    $specs['phone']           = "numchar length|10 mask|$phone_mask";
    $specs['fax']             = "numchar length|10 mask|$phone_mask";
    $specs['siret']           = 'code siret seekable';
    $specs['email']           = 'email';
    $specs['contact_name']    = 'str seekable';
    $specs['carriage_paid']   = 'str';
    $specs['delivery_time']   = 'str';
    $specs['departments']     = 'text'; // not str, as it could be longer than 255 chars
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
    $this->_departments = explode("|", $this->departments);
    CMbArray::removeValue("", $this->_departments);
    
    if (count($this->_departments)) {
      $this->_view .= " (".implode(", ", $this->_departments).")";
    }
    
    $this->_is_supplier = $this->countBackRefs("product_references") > 0;
    $this->_is_manufacturer = $this->countBackRefs("products") > 0;
  }
  
  static function getManufacturers($also_inactive = true){
    $societe = new self;
    $list = $societe->loadList(null, "name");
    foreach($list as $_id => $_societe) {
      if (!($_societe->_is_manufacturer || $also_inactive && !$_societe->_is_supplier)) {
        unset($list[$_id]);
      }
    }
    return $list;
  }
  
  static function getSuppliers($also_inactive = true){
    $societe = new self;
    $list = $societe->loadList(null, "name");
    foreach($list as $_id => $_societe) {
      if (!($_societe->_is_supplier || $also_inactive && !$_societe->_is_manufacturer)) {
        unset($list[$_id]);
      }
    }
    return $list;
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    if ($this->_departments) {
      foreach($this->_departments as &$_dep) {
        $_dep = str_pad($_dep, 2, "0", STR_PAD_LEFT);
      }
      $this->departments = implode("|", $this->_departments);
    }
  }

  function loadRefsBack() {
    $ljoin = array(
      "product" => "product_reference.product_id = product.product_id"
    );
    $where = array(
      "product_reference.societe_id" => " = '$this->_id'"
    );
    $reference = new CProductReference;
    $this->_ref_product_references = $reference->loadList($where, "product.name", null, null, $ljoin);
    $this->_back["product_references"] = $this->_ref_product_references;
    
    $this->_ref_products = $this->loadBackRefs('products', "name");
    $this->_ref_product_orders = $this->loadBackRefs('product_orders', "date_ordered");
  }
}
