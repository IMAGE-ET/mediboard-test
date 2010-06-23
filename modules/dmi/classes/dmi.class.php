<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dmi', 'produit_prescriptible');

class CDMI extends CProduitPrescriptible {
  static $code128_prefixes = array(
    "00"  => "Serial Shipping Container Code",
    "01"  => "Shipping Container Code",
    "10"  => "Batch or Lot Number",
    "11"  => "Production Date (YYMMDD)",
    "13"  => "Packaging Date (YYMMDD)",
    "15"  => "Best Before/Sell By Date (YYMMDD)",
    "17"  => "Sell By/Expiration Date (YYMMDD)",
    "20"  => "Product Variant",
    "21"  => "Serial Number",
    "22"  => "HIBCC; quantity, date, batch, and link",
    "23"  => "Lot number",
    "240" => "Secondary product attributes",
    "250" => "Secondary Serial number",
    "30"  => "Quantity each",
    "310" => "Net Weight, kilograms",
    "311" => "Length or first dimension, meters",
    "312" => "Width, diameter, or 2nd dimension, meters",
    "313" => "Depth, thickness, height, or 3rd dimension, meters",
    "314" => "Area, square meters",
    "315" => "Volume, liters",
    "316" => "Volume, cubic meters",
    "320" => "Net weight, pounds",
    "330" => "Gross weight, kilograms",
    "331" => "Length or first dimension, meters logistics",
    "332" => "Width, diameter, or 2nd dimension, meters logistics",
    "333" => "Depth, thickness, height, or 3rd dimension, meters logistics",
    "334" => "Area, square meters logistics",
    "335" => "Gross volume, liters logistics",
    "336" => "Gross volume, cubic meters logistics",
    "340" => "Gross weight, pounds",
    "400" => "Customer purchase order number",
    "410" => "Ship to location code (EAN-13 or DUNS)",
    "411" => "Bill to location code (EAN-13 or DUNS)",
    "412" => "Purchase from location code (EAN-13 or DUNS)",
    "420" => "Ship to postal code",
    "421" => "Ship to postal code with 3-digit ISO country code",
    "8001"=> "Roll products => width, length, core diameter, direction, splices",
    "8002"=> "Electronic serial number for cellular telephones",
    "90"  => "FACT identifiers (internal applications)",
    "91"  => "Internal use (raw materials, packaging, components)",
    "92"  => "Internal use (raw materials, packaging, components)",
    "93"  => "Internal use (product manufacturers)",
    "94"  => "Internal use (product manufacturers)",
    "95"  => "SCAC+Carrier PRO number",
    "96"  => "SCAC+Carrier assigned container ID",
    "97"  => "Internal use (wholesalers)",
    "98"  => "Internal use (retailers)",
    "99"  => "Mutually defined text",
  );
  
  // DB Table key
  var $dmi_id  = null;
  
  // DB Fields
  var $category_id = null;
  var $code_lpp    = null;
  var $type        = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi';
    $spec->key   = 'dmi_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["category_id"] = "ref notNull class|CDMICategory autocomplete|nom";
    $specs["code_lpp"]    = "str protected";
    $specs["type"]        = "enum notNull list|purchase|loan|deposit default|deposit"; // achat/pret/depot
    return $specs;
  }
       
  function loadGroupList() {
    $group = CGroups::loadCurrent();
    $dmi_category = new CDMICategory();
   	$dmi_category->group_id = $group->_id;
   	$dmi_categories_list = $dmi_category->loadMatchingList('nom');
   	$dmi_list = array();
   	foreach ($dmi_categories_list as &$cat) {
   	  $cat->loadRefsElements();
   	  $dmi_list = array_merge($dmi_list, $cat->_ref_elements);
   	}
    return $dmi_list;
  }
  
  function check(){
    if(!$this->_id){
      $group_id = CGroups::loadCurrent()->_id;
      $dmi = new CDMI();
      $ljoin["dmi_category"] = "dmi_category.category_id = dmi.category_id";
      $where["code"] = " = '$this->code'";
      $where["dmi_category.group_id"] = " = '$group_id'";
      $dmi->loadObject($where, null, null, $ljoin);
      if($dmi->_id){
        return "Un DMI possède déjà le code produit suivant: $this->code";
      }
    }
    return parent::check();
  }
}

?>