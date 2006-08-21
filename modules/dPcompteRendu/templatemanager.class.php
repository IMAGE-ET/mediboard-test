<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

require_once( $AppUI->getModuleClass("dPcompteRendu", "compteRendu") );
require_once( $AppUI->getModuleClass("dPcompteRendu", "listeChoix") );
require_once( $AppUI->getModuleClass("dPcompteRendu", "aidesaisie") );
require_once( $AppUI->getModuleClass("dPplanningOp" , "planning") );
require_once( $AppUI->getModuleClass("dPcabinet"    , "consultation") );
require_once( $AppUI->getModuleClass("dPpatients"   , "patients") );
require_once( $AppUI->getModuleClass("mediusers"    , "functions"));
require_once( $AppUI->getModuleClass("mediusers"));
require_once( $AppUI->getSystemClass("smartydp"));

define("TMT_CONSULTATION"   , "consultation"   );
define("TMT_HOSPITALISATION", "hospitalisation");
define("TMT_OPERATION"      , "operation"      );
define("TMT_AUTRE"          , "autre"          );

$listTypes = array();
$listType[] = TMT_CONSULTATION;
$listType[] = TMT_HOSPITALISATION;
$listType[] = TMT_OPERATION;
$listType[] = TMT_AUTRE;

class CTemplateManager {
  var $editor = "fckeditor";
  
  var $properties = array();
  var $helpers = array();
  var $lists = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  
  var $valueMode = true; // @todo : changer en applyMode
  
  function CTemplateManager() {
  }

  function makeSpan($spanClass, $text) {
    // Escape entities cuz FCKEditor does so
    $text = htmlentities($text);
    
    // Keep backslashed double quotes instead of quotes 
    // cuz FCKEditor creates double quoted attributes
    $html = "<span class=\"{$spanClass}\">{$text}</span>";
    
    return $html; 
  }
  
  function addProperty($field, $value = null) {
    $this->properties[$field] = array (
      "field" => $field,
      "value" => $value,
      // @todo : passer en regexp
      //"fieldHTML" => $this->makeSpan("field", "[{$field}]"),
      //"valueHTML" => $this->makeSpan("value", "{$value}"));
      "fieldHTML" => htmlentities("[{$field}]"),
      "valueHTML" => $value);
  }

  function addList($name, $choice = null) {
    $this->lists[$name] = array (
      "name" => $name,
      // @todo : passer en regexp
      //"nameHTML" => $this->makeSpan("name", "[Liste - {$name}]"));
      "nameHTML" => htmlentities("[Liste - {$name}]"));
  } 
  
  function addHelper($name, $text) {
    $this->helpers[$name] = $text;
  }
  
  function applyTemplate($template) {
    assert(is_a($template, "CCompteRendu") || is_a($template, "CPack"));
    
    if(is_a($template, "CCompteRendu")) {
    
      if (!$this->valueMode) {
        $this->SetFields($template->type, $template->chir_id);
      }

      $this->renderDocument($template->source);
    
    } else {
    
      if (!$this->valueMode) {
        $this->SetFields("hospitalisation", $template->chir_id);
      }

      $this->renderDocument($template->_source);
    }
  }
  
  function initHTMLArea () {
    // Don't use mbSetValue which uses $m
    $_SESSION["dPcompteRendu"]["templateManager"] = $this;
   
    $smarty = new CSmartyDP(1);
    $smarty->template_dir = "modules/dPcompteRendu/templates/";
    $smarty->compile_dir = "modules/dPcompteRendu/templates_c/";
    $smarty->config_dir = "modules/dPcompteRendu/configs/";
    $smarty->cache_dir = "modules/dPcompteRendu/cache/";
    $smarty->assign("templateManager", $this);
    $smarty->display("init_htmlarea.tpl");
	}
  
  function setFields($modeleType) {
    // Général Patient
    $patient = new CPatient;
    $patient->fillTemplate($this);
    // Général Praticien
    $prat = new CMediusers();
    $prat->fillTemplate($this);
        
    switch ($modeleType) {
      case TMT_CONSULTATION   : $object = new CConsultation; break;
      case TMT_OPERATION      : $object = new COperation   ; break;
      case TMT_HOSPITALISATION: $object = new COperation   ; break;
    }
    
    if(isset($object))
      $object->fillTemplate($this);
  }
  
  function loadLists($user_id, $compte_rendu_id = 0) {
    // Liste de choix
    $chir = new CMediusers;
    $chir->load($user_id);
    $where = array();
    $where[] = "(chir_id = '$chir->user_id' OR function_id = '$chir->function_id')";
    $where["compte_rendu_id"] = "IN ('0', '$compte_rendu_id')";
    
    $lists = new CListeChoix();
    $lists = $lists->loadList($where);
    
    foreach ($lists as $list) {
      $this->addList($list->nom);
    }
  }
  
  function loadHelpers($user_id, $modeleType) {
    switch($modeleType) {
      case TMT_CONSULTATION   : $object = new CConsultation; break;
      case TMT_OPERATION      : $object = new COperation   ; break;
      case TMT_HOSPITALISATION: $object = new COperation   ; break;
    }
    
    if(isset($object)) {
      $object->loadAides($user_id);
      if(is_array($helpers = @$object->_aides["compte_rendu"])) {
        // Caution, keys and values have to been flipped out
        $this->helpers = array_flip($helpers);
      }
    } else {
      $this->helpers = array();
    }
  }
  
  function renderDocument($source) {
    
    foreach($this->properties as $property) {
      $fields[] = $property["fieldHTML"];
      $values[] = $property["valueHTML"];
    }
    $this->document = str_replace($fields, $values, $source);
  }
  
  // Obtention des listes utilisées dans le document
  function getUsedLists($lists) {
  	$this->usedLists = array();
    foreach($lists as $key => $value) {
      $pos = strpos($this->document, htmlentities(stripslashes("[Liste - $value->nom]")));
      if($pos !== false) {
        $this->usedLists[$pos] = $value;
      }
    }
    ksort($this->usedLists);
    return $this->usedLists;
  }
}
?>