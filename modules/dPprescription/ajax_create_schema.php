<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$classes =
array("CPrescriptionLineMix", "CPrescriptionLineMixItem", "CPrescriptionLineMixVariation",
        "CPrescriptionLineComment", "CPrescriptionLineElement", "CPrescriptionLineMedicament",
        "CPrisePosologie");

$types = array(
  "date"     => "MBdate",
  "dateTime" => "MBdateTime",
  "time"     => "MBtime",
  "bool"     => "MBbool",
  "float"    => "MBfloat",
  "currency" => "MBfloat",
  "numchar"  => "MBint",
  "num"      => "MBint",
  "pct"      => "MBfloat",
);

function createSimpleType($xsd, $name, $pattern, $base = "xsd:string") {
  $type_node = $xsd->createElement("xsd:simpleType");
  $type_node->setAttribute("name", $name);
    $restr = $xsd->createElement("xsd:restriction");
    $restr->setAttribute("base", $base);
      $patt = $xsd->createElement("xsd:pattern");
      $patt->setAttribute("value", $pattern);
    $restr->appendChild($patt);
  $type_node->appendChild($restr);
  
  return $type_node;
}

function addComment($node, $comment) {
  $node->appendChild($GLOBALS["xsd"]->createComment(utf8_encode($comment)));
}

$xsd = new DOMDocument();
$xsd->formatOutput = true;
$root = $xsd->createElementNS("http://www.w3.org/2001/XMLSchema", "xsd:schema");
$xsd->appendChild($root);
$GLOBALS["xsd"] = $xsd;

// Déclaration des nouveaux types de données
addComment($root, "Types de base permettant des valeurs vides");
$root->appendChild(createSimpleType($xsd, "MBdate",     "([0-9]{4}-[0-9]{2}-[0-9]{2})?"));
$root->appendChild(createSimpleType($xsd, "MBdateTime", "([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})?"));
$root->appendChild(createSimpleType($xsd, "MBtime",     "([0-9]{2}:[0-9]{2}:[0-9]{2})?"));
$root->appendChild(createSimpleType($xsd, "MBbool",     "[01]?"));
$root->appendChild(createSimpleType($xsd, "MBfloat",    "(-?[0-9]+(\.[0-9]+)?)?"));
$root->appendChild(createSimpleType($xsd, "MBint",      "(-?[0-9]+)?"));

// Déclaration du type CMbObject
$mbObjectType = $xsd->createElement("xsd:complexType");
$mbObjectType->setAttribute("name", "CMbObjectType");

// Ajout de l'attribut ID
$id_node = $xsd->createElement("xsd:attribute");
$id_node->setAttribute("name", "id");
$id_node->setAttribute("type", "xsd:ID");
$mbObjectType->appendChild($id_node);

addComment($root, "Type CMbObjectType de la classe CMbObject");
$root->appendChild($mbObjectType);

$mbObjectElement = $xsd->createElement("xsd:element");
$mbObjectElement->setAttribute("name", "CMbObject");
$mbObjectElement->setAttribute("type", "CMbObjectType");
$mbObjectElement->setAttribute("abstract", "true");

addComment($root, "Classe CMbObject parente de toutes les classes exportées");
$root->appendChild($mbObjectElement);

// Ajout des elements pour les classes
foreach($classes as $class) {
  $element = $xsd->createElement("xsd:element");
  $element->setAttribute("name", $class);
  $element->setAttribute("substitutionGroup", "CMbObject");
  
  $complexType = $xsd->createElement("xsd:complexType");
    $complexContent = $xsd->createElement("xsd:complexContent");
      $extension = $xsd->createElement("xsd:extension");
      $extension->setAttribute("base", "CMbObjectType");
        $sequence = $xsd->createElement("xsd:all");
      $extension->appendChild($sequence);
    $complexContent->appendChild($extension);
  $complexType->appendChild($complexContent);
  
  $instance = new $class;
    
  foreach($instance->getDBFields() as $field => $value) {
    $type = $instance->_specs[$field]->getSpecType();
    
    // get the corresponding XML type, "string" by default
    $xml_type = CValue::read($types, $type, "xsd:string");
    
    switch($type) {
      case "ref":
        $field_node = $xsd->createElement("xsd:attribute");
        $field_node->setAttribute("name", $field);
        $field_node->setAttribute("type", "xsd:string");
      break;
      
      case "enum":
        $field_node = $xsd->createElement("xsd:element");
        $field_node->setAttribute("name", $field);
        
        $simpleType = $xsd->createElement("xsd:simpleType");
        
        $restriction = $xsd->createElement("xsd:restriction");
        $restriction->setAttribute("base", "xsd:string");
        if (!$instance->_specs[$field]->notNull) {
          $instance->_specs[$field]->_list[] = "";
        }
        foreach($instance->_specs[$field]->_list as $_value) {
          $enumeration = $xsd->createElement("xsd:enumeration");
          $enumeration->setAttribute("value", $_value);
          
          addComment($restriction, CAppUI::tr("$class.$field.$_value"));
          $restriction->appendChild($enumeration);
        }
        
        $simpleType->appendChild($restriction);
        $field_node->appendChild($simpleType);
      break;
      
      default: 
        $field_node = $xsd->createElement("xsd:element");
        $field_node->setAttribute("name", $field);
        $field_node->setAttribute("type", $xml_type);
    }
    
    if (!$instance->_specs[$field]->notNull && ($field != $instance->_spec->key)) {
      if ($type === "ref")
        $field_node->setAttribute("use", "optional");
      else
        $field_node->setAttribute("minOccurs", 0);
    }
     
    $parent = ($type === "ref") ? $extension : $sequence;
    
    addComment($parent, CAppUI::tr("$class-$field"));
    $parent->appendChild($field_node);
  }
  
  $element->appendChild($complexType);
  
  addComment($root, "==== ".CAppUI::tr($class)." ====");
  $root->appendChild($element);
}

$export = $xsd->createElement("xsd:element");
$export->setAttribute("name", "CPrescription");
$root->appendChild($export);

$complexType = $xsd->createElement("xsd:complexType");
$export->appendChild($complexType);

$sequence = $xsd->createElement("xsd:sequence");
$complexType->appendChild($sequence);

$class_node = $xsd->createElement("xsd:element");
$class_node->setAttribute("ref", "CMbObject");
$class_node->setAttribute("minOccurs", 0);
$class_node->setAttribute("maxOccurs", "unbounded");
$sequence->appendChild($class_node);

$libelle_node = $xsd->createElement("xsd:attribute");
$libelle_node->setAttribute("name", "libelle");
$libelle_node->setAttribute("type", "xsd:string");
$complexType->appendChild($libelle_node);

$type_node = $xsd->createElement("xsd:attribute");
$type_node->setAttribute("name", "type");
$type_node->setAttribute("type", "xsd:string");
$complexType->appendChild($type_node);

$object_class_node = $xsd->createElement("xsd:attribute");
$object_class_node->setAttribute("name", "object_class");
$object_class_node->setAttribute("type", "xsd:string");
$complexType->appendChild($object_class_node);

$fast_access_node = $xsd->createElement("xsd:attribute");
$fast_access_node->setAttribute("name", "fast_access");
$fast_access_node->setAttribute("type", "xsd:string");
$complexType->appendChild($fast_access_node);

$debug = false;

if ($debug) {
  header("Content-Type: text/plain");
}
else {
  header("Content-Type: application/xml");
  header("Content-Disposition: attachment; filename=prescription.xsd");
}

echo $xsd->saveXML();

?>