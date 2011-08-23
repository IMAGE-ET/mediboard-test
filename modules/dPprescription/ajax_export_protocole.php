<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$stream_xml = CValue::get("stream_xml", 1);

// Chargement de la prescription
$prescription_id = CValue::get("prescription_id");
$prescription = new CPrescription;
$prescription->load($prescription_id);

$doc = new CMbXMLDocument(null);

$root = $doc->createElement("CPrescription");

$attributes = array("libelle", "object_class", "type", "fast_access");
foreach($attributes as $_attribute) {
  ${$_attribute . "_att"} = $doc->createAttribute($_attribute);
  ${$_attribute . "_value"} = $doc->createTextNode(utf8_encode($prescription->$_attribute));
  ${$_attribute . "_att"}->appendChild(${$_attribute . "_value"});
  $root->appendChild(${$_attribute . "_att"});
}

$doc->appendChild($root);

if (!function_exists('exportXML')) {
  function exportXML($object, &$doc, $substitution_id = 0, $id_line = 0) {
    // Propriétés à insérer comme attributs
    $keys =
      array("prescription_line_medicament_id", "prise_posologie_id", "prescription_line_mix_id",
            "prescription_line_mix_item_id", "prescription_line_mix_variation_id", "prescription_line_element_id",
            "prescription_id", "substitution_line_id", "moment_unitaire_id", "substitute_for_id", "praticien_id",
            "creator_id", "child_id", "operation_id", "object_id", "executant_prescription_line_id",
            "user_executant_id", "next_line_id", "prescription_line_comment_id", "protocole_id");
    $fields = $object->getPlainFields();
    $class = get_class($object);
    $$class = $doc->createElement($class);
  
    foreach ($fields as $key => $field) {
      if (in_array($key, $keys)) {
        ${$key}   = $doc->createAttribute($key);
        $id_value = $doc->createTextNode("id-".$object->$key);
        ${$key}->appendChild($id_value);
        ${$class}->appendChild(${$key});
      }
      else {
        if ($class == "CPrescriptionLineComment" && $key == "category_prescription_id") {
          $category = new CCategoryPrescription;
          $category->load($object->$key);
          $value_category = "";
          if ($category->_id) {
            $value_category = utf8_encode($category->chapitre . "#" . $category->nom);
          }
          ${$key} = $doc->createAttribute($key);
          $id_value = $doc->createTextNode($value_category);
          ${$key}->appendChild($id_value);
          ${$class}->appendChild(${$key});
        }
        else if ($class == "CPrescriptionLineElement" && $key == "element_prescription_id") {
          $element_prescription = new CElementPrescription;
          $element_prescription->load($object->$key);
          $element_prescription->loadRefCategory();
          $category = $element_prescription->_ref_category_prescription;
          ${$key} = $doc->createAttribute($key);
          $id_value = $doc->createTextNode(utf8_encode($element_prescription->libelle . "#" . $category->chapitre . "#" . $category->nom));
          ${$key}->appendChild($id_value);
          ${$class}->appendChild(${$key});
        }
        else {
          ${$key} = $doc->createElement($key, utf8_encode($object->$key));
        }
      }
      ${$class}->appendChild(${$key});
    }
    $doc->documentElement->appendChild(${$class});
  }
}
// Chargement des lignes 
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElement();
$prescription->loadRefsLinesAllComments();
$prescription->loadRefsPrescriptionLineMixes();

foreach ($prescription->_ref_prescription_lines as $_line) {
  exportXML($_line, $doc);
  
  $_line->loadRefsPrises();
  foreach($_line->_ref_prises as $_ref_prise) {
    exportXML($_ref_prise, $doc, null, $_line->_id);
  }
  
  if (!$_line->substitute_for_id) {
    $_line->loadRefsSubstitutionLines();
    
    foreach ($_line->_ref_substitution_lines["CPrescriptionLineMedicament"] as $_ref_line_med) {
      exportXML($_ref_line_med, $doc, $_line->_id);
      
      $_ref_line_med->loadRefsPrises();
      foreach($_ref_line_med->_ref_prises as $_ref_prise) {
        exportXML($_ref_prise, $doc, null, $_ref_line_med->_id);
      }
    }
    
    foreach ($_line->_ref_substitution_lines["CPrescriptionLineMix"] as $_ref_line_mix) {
      exportXML($_ref_line_mix, $doc, $_line->_id);
      foreach($_ref_line_mix->_ref_lines as $_ref_line) {
        exportXML($_ref_line, $doc);
      }
    }
  }
}

foreach ($prescription->_ref_prescription_lines_element as $_line) {
  exportXML($_line, $doc);
  $_line->loadRefsPrises();
  foreach($_line->_ref_prises as $_ref_prise) {
    exportXML($_ref_prise, $doc, null, $_line->_id);
  }
}

foreach ($prescription->_ref_prescription_lines_all_comments as $_line) {
  exportXML($_line, $doc);
}

foreach ($prescription->_ref_prescription_line_mixes as $_line) {
  exportXML($_line, $doc);
    
  $_line->loadRefsLines();
  
  foreach($_line->_ref_lines as $_ref_line) {
    exportXML($_ref_line, $doc);
  }
  
  if (!$_line->substitute_for_id) {
    $_line->loadRefsSubstitutionLines();
    
    foreach ($_line->_ref_substitution_lines["CPrescriptionLineMedicament"] as $_ref_line_med) {
      exportXML($_ref_line_med, $doc, $_line->_id);
      $_ref_line_med->loadRefsPrises();
      foreach($_ref_line_med->_ref_prises as $_ref_prise) {
        exportXML($_ref_prise, $doc, null, $_ref_line_med->_id);
      }
    }
    
    foreach ($_line->_ref_substitution_lines["CPrescriptionLineMix"] as $_ref_line_mix) {
      exportXML($_ref_line_mix, $doc, $_line->_id);
      foreach($_ref_line_mix->_ref_lines as $_ref_line) {
        exportXML($_ref_line, $doc);
      }
    }
  }
}

$doc->setSchema("modules/dPprescription/xml/prescription.xsd");
$doc->purgeEmptyElements();

$valid = $doc->schemaValidate();
if (!$valid) {
  CAppUI::stepAjax("Document XML mal formé", UI_MSG_ERROR);
  CApp::rip();
}

$content = $doc->saveXML();

if ($stream_xml) {
  header('Content-Type: application/xml');
  header('Content-Disposition: attachment; filename="'.$prescription->libelle.'.xml"');
  header('Content-Length: '.strlen($content).';');
}

echo $content;

?>