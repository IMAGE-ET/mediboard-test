<?php /* $Id:$ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = CValue::post("praticien_id", null);
$function_id  = CValue::post("function_id" , null);
$group_id     = CValue::post("group_id"    , null);

$correspondances_ids =
  array("CPrescriptionLineMedicament" => "prescription_line_medicament_id",
        "CPrescriptionLineMix"        => "prescription_line_mix_id",
        "CPrescriptionLineElement"    => "prescription_line_element_id",
        "CPrescriptionLineComment"    => "prescription_line_comment_id");

$file = $_FILES['datafile'];
$message = "";
$imported = 0;

// On vérifié l'extension du fichier fourni

if (strtolower(pathinfo($file['name'] , PATHINFO_EXTENSION) == 'xml')) {
  $doc = file_get_contents($file['tmp_name']);
  
  $xml = new CMbXMLDocument(null);
  $xml->loadXML($doc);
  
  $root = $xml->firstChild;
  
  // Plusieurs protocoles
  if ($root->nodeName == "protocoles") {
    $root = $root->childNodes;
    // Si l'un des noeuds prescription n'est pas valide, pas d'import
    foreach($root as $_prescription) {
      $temp_doc = new CMbXMLDocument(null);
      $temp_doc->loadXML(utf8_encode($xml->saveXML($_prescription)));
      $temp_doc->setSchema("modules/dPprescription/xml/prescription.xsd");
      $valid = $temp_doc->schemaValidate();
      if (!$valid) {
        CAppUI::stepAjax("Document XML mal formé", UI_MSG_ERROR);
        CApp::rip();
      } 
    }
  }
  // Un seul protocole
  else {
    // Si non valide, pas d'import
    $xml->setSchema("modules/dPprescription/xml/prescription.xsd");
    $xml->purgeEmptyElements();
    
    $valid = $xml->schemaValidate();
    if (!$valid) {
      CAppUI::stepAjax("Document XML mal formé", UI_MSG_ERROR);
      CApp::rip();
    }
    $root = array();
    $root[] = $xml->firstChild;
  }

  foreach ($root as $_prescription) {
    $prescription = new CPrescription();
    $prescription->object_class = $_prescription->getAttribute("object_class");
    $prescription->libelle      = utf8_decode($_prescription->getAttribute("libelle"));
    $prescription->type         = $_prescription->getAttribute("type");
    $prescription->fast_access  = $_prescription->getAttribute("fast_access");
    $prescription->praticien_id = $praticien_id;
    $prescription->function_id  = $function_id;
    $prescription->group_id     = $group_id;
    
    if ($msg = $prescription->store()) {
      $message .= $msg . "<br/>";
      CApp::rip();
    }
    
    $imported ++;
    
    $current_id_object  = null;
    $substitute_for_id  = null;
    $save_substitute_id = null;
    $save_line_mix_id   = null;
    
    $classes = array("CPrescriptionLineMixItem", "CPrisePosologie");
    
    foreach ($_prescription->childNodes as $class) {
      // Escape des nodes de type text
      if ($class->nodeName === "#text") continue;
      
      $object = new $class->nodeName;
      
      if (($class->nodeName == "CPrescriptionLineMix" || $class->nodeName == "CPrescriptionLineMedicament")) {
        $substitute_for_id = substr($class->getAttribute("substitute_for_id"), 3);
        // On réinitialise la sauvegarde de substitute_for_id si c'est pas une ligne de substitution
        if (!$substitute_for_id) {
          $save_substitute_id = null;
        }
      }
      
      if ($class->nodeName == "CPrescriptionLineElement") {
        $save_substitute_id = null;
        $substitute_for_id = null;
      }
      
      foreach ($class->childNodes as $_property) {
        if ($_property->nodeName === "#text") continue;
        $object->{$_property->nodeName} = utf8_decode($_property->nodeValue);
      }
      
      switch($class->nodeName) {
        case "CPrescriptionLineMedicament":
        case "CPrescriptionLineMix":
          $object->creator_id = CAppUI::$user->_id;
          $object->prescription_id = $prescription->_id;
          if ($substitute_for_id) {
            $object->substitute_for_id = $current_id_object;
          }
          break;
        case "CPrescriptionLineComment":
          $object->prescription_id = $prescription->_id;
          $object->creator_id = CAppUI::$user->_id;
          $category_prescription_id = utf8_decode($class->getAttribute("category_prescription_id"));
          if ($category_prescription_id != "") {
            list($chapitre, $nom) = explode("#", $category_prescription_id);
            // On vérifie l'existence de la catégorie pour la ligne de commentaire
            $category_prescription = new CCategoryPrescription;
            $category_prescription->chapitre = addslashes($chapitre);
            $category_prescription->nom      = addslashes($nom);
            
            if (!$category_prescription->loadMatchingObject()) {
              if ($msg = $category_prescription->store()) {
                $message .= $msg . "<br/>";
              }
            }
            
            $object->category_prescription_id = $category_prescription->_id;
          }
          break;
        case "CPrescriptionLineMixItem":
          $object->prescription_line_mix_id = $save_line_mix_id;
          break;
        case "CPrescriptionLineElement":
          $object->creator_id = CAppUI::$user->_id;
          $object->prescription_id = $prescription->_id;
          
          // Comme pour la ligne de commentaire, on teste pour l'élémént de prescription et la catégorie
          list($libelle, $chapitre, $nom) = explode("#", utf8_decode($class->getAttribute("element_prescription_id")));
          $category_prescription = new CCategoryPrescription;
          $category_prescription->chapitre = addslashes($chapitre);
          $category_prescription->nom      = addslashes($nom);
          
          if (!$category_prescription->loadMatchingObject()) {
            if ($msg = $category_prescription->store()) {
              $message .= $msg . "<br/>";
            }
          }
          
          $element_presc = new CElementPrescription;
          $element_presc->libelle = addslashes($libelle);
  
          if (!$element_presc->loadMatchingObject()) {
            $element_presc->category_prescription_id = $category_prescription->_id;
            
            if ($msg = $element_presc->store()) {
              $message .= $msg . "<br/>";
            }
          }
          $object->element_prescription_id = $element_presc->_id;
          break;
        case 'CPrisePosologie':
          // Si c'est une ligne de substitution
          $object->object_id = $save_substitute_id ? $save_substitute_id : $current_id_object;
          
          $moment_unitaire_id = explode("-", $class->getAttribute("moment_unitaire_id"));
          $object->moment_unitaire_id = $moment_unitaire_id[1];
      }
      
      if ($msg = $object->store()) {
        $message .= $msg . "<br/>";
      }
      
      // Si c'est une ligne de substitution, il faut conserver son id pour les posologies
      if (($class->nodeName == "CPrescriptionLineMix" || $class->nodeName == "CPrescriptionLineMedicament")
          && $substitute_for_id) {
        $save_substitute_id = $substitute_for_id ? $object->_id : 0;
      }
      
      // On sauvegarde la line_mix_id pour les line_mix_items
      if ($class->nodeName == "CPrescriptionLineMix") {
        $save_line_mix_id = $object->_id;
      }
  
      // Si ce n'est pas une ligne de substitution, on sauvegarde l'id pour les posologies
      if (!in_array($class->nodeName, $classes) && !$substitute_for_id) {
        $current_id_object  = $object->_id;
      }
    }
  }
  CAppUI::stepAjax($message . $imported. " protocole(s) importé(s)");
  echo "<script type='text/javascript'>
        try {
          window.opener.Protocole.refreshList(".$prescription->_id.");
          window.opener.Protocole.edit(".$prescription->_id.")
        } catch(e) {}
     </script>";
}
else {
  CAppUI::stepAjax("Aucun protocole importé");
}

?>