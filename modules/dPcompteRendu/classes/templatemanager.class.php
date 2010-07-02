<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

class CTemplateManager {
  var $editor = "fckeditor";
  
  var $sections = array();
  var $helpers = array();
  var $lists = array();
  var $graphs = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  var $isCourrier = null;
  
  var $valueMode = true; // @todo : changer en applyMode
  var $printMode = false;
  var $simplifyMode = false;
  var $parameters = array();
  
  private static $barcodeCache = array();
  
  function CTemplateManager($parameters = array()) {
    global $AppUI;
    $user = new CMediusers();
    $user->load($AppUI->user_id);
		
  	$this->parameters = $parameters;
		
    $this->addProperty("Courrier - nom destinataire"     , "[Courrier - nom destinataire]");
    $this->addProperty("Courrier - adresse destinataire" , "[Courrier - adresse destinataire]");
    $this->addProperty("Courrier - cp ville destinataire", "[Courrier - cp ville destinataire]");
    $this->addProperty("Courrier - copie à"              , "[Courrier - copie à]");

    $this->addProperty("Général - date du jour"  , mbTransformTime(null, null, CAppUI::conf("date")));
    $this->addProperty("Général - heure courante", mbTransformTime(null, null, CAppUI::conf("time")));
    $this->addProperty("Général - rédacteur"     , $user->_shortview);
  }
	
  function getParameter($name, $default = null) {
    return CValue::read($this->parameters, $name, $default);
  }

  function makeSpan($spanClass, $text) {
    // Escape entities cuz FCKEditor does so
    $text = htmlentities($text);
    
    // Keep backslashed double quotes instead of quotes 
    // cuz FCKEditor creates double quoted attributes
    return "<span class=\"{$spanClass}\">{$text}</span>";
  }
  
  function addProperty($field, $value = null, $options = array()) {
  	$sec = explode(' - ', $field, 2);
  	if (count($sec) > 1) {
  		$section = $sec[0];
  		$item = $sec[1];
  	} else {
  		$section = ' ';
  		$item = $sec;
  	}
  	
  	if (!array_key_exists($section, $this->sections)) {
  		$this->sections[$section] = array();
  	}
  	$this->sections[$section][$field] = array (
  	  "view"      => htmlentities($item),
      "field"     => $field,
      "value"     => $value,
      "fieldHTML" => htmlentities("[{$field}]"),
      "valueHTML" => $value,
      "options"   => $options
    );
    
    if (isset($options["barcode"])) {
      $_field = &$this->sections[$section][$field];
      
      if ($this->valueMode)
        $src = $this->getBarcodeDataUri($_field['value'], $options["barcode"]);
      else 
        $src = $_field['fieldHTML'];
      
      $_field["field"]  = "<img alt=\"$field\" src=\"$src\" ";
      
      foreach($options["barcode"] as $name => $attribute) {
        $_field["field"] .= " $name=\"$attribute\"";
      }
      
      $_field["field"] .= "/>";
    }
  }
  
  function addDateProperty($field, $value = null) {
    $value = $value ? mbTransformTime(null, $value, CAppUI::conf("date")) : "";
    $this->addProperty($field, $value);
  }
  
  function addLongDateProperty($field, $value) {
    $value = ucfirst(mbTransformTime(null, $value, CAppUI::conf("longdate")));
    $this->addProperty($field, $value);
  }
  
  function addTimeProperty($field, $value = null) {
    $value = $value ? mbTransformTime(null, $value, CAppUI::conf("time")) : "";
    $this->addProperty($field, $value);
  }
  
  function addDateTimeProperty($field, $value = null) {
    $value = $value ? mbTransformTime(null, $value, CAppUI::conf("datetime")) : "";
    $this->addProperty($field, $value);
  }
	
  function addListProperty($field, $list = null) {
    if (!is_array($list)) $list = array($list);
		$str = "<ul>";
		if (count($list)) {
			$str .= "<li>" . implode("</li><li>", $list) . "</li>";
		}
		$str .= "</ul>";
    $this->addProperty($field, $str);
  }
	
  function addGraph($field, $data, $options = array()) {
    $this->graphs[utf8_encode($field)] = array(
      "data" => $data, 
      "options" => $options, 
      "name" => utf8_encode($field)
    );
		
		$this->addProperty($field, $field);
  }
  
  function addBarcode($field, $data, $options = array()) {
    $options = array(
      "barcode" => array(
        "width"  => 150,
        "height" => 60,
        "class"  => "barcode"
      )
    );
    $this->addProperty($field, $data, $options);
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
    assert($template instanceof CCompteRendu || $template instanceof CPack);
    
    if($template instanceof CCompteRendu) {
      
      if (!$this->valueMode) {
        $this->setFields($template->object_class);
      }

      $this->renderDocument($template->_source);
    
    } else {
      /** FIXME: ?? */
      if (!$this->valueMode) {
        $this->setFields("hospitalisation");
      }

      $this->renderDocument($template->_source);
    }
  }
  
  function initHTMLArea () {
    // Don't use CValue::setSession which uses $m
    $_SESSION["dPcompteRendu"]["templateManager"] = serialize($this);
   
    $smarty = new CSmartyDP("modules/dPcompteRendu");
    $smarty->assign("templateManager", $this);
    $smarty->display("init_htmlarea.tpl");
  }
  
  function setFields($modeleType) {
    if ($modeleType){
      $object = new $modeleType;
      $object->fillTemplate($this);
    }
  }
  
  function loadLists($user_id, $compte_rendu_id = 0) {
    // Liste de choix
    $user = new CMediusers;
    $compte_rendu = new CCompteRendu();
    $compte_rendu->load($compte_rendu_id);
    
    $where = array();
    if($user_id){
      $user->load($user_id);
      $user->loadRefFunction();
      $where[] = "(
        chir_id = '$user->user_id' OR 
        function_id = '$user->function_id' OR 
        group_id = '{$user->_ref_function->group_id}'
      )";
    }
    else {
      global $AppUI;
      $user->load($AppUI->user_id);
      $user->loadRefFunction();
      $where[] = "(
        function_id IN('$user->function_id', '$compte_rendu->function_id') OR 
        group_id IN('{$user->_ref_function->group_id}', '$compte_rendu->group_id')
      )";
    }
    
    $where[] = $compte_rendu->_spec->ds->prepare("`compte_rendu_id` IS NULL OR compte_rendu_id = %",$compte_rendu_id); 
    $order = "nom ASC";
    $lists = new CListeChoix();
    $lists = $lists->loadList($where, $order);
    foreach ($lists as $list) {
      $this->addList($list->nom);
    }
  }
  
  function loadHelpers($user_id, $modeleType) {
    $compte_rendu = new CCompteRendu();
    $ds = $compte_rendu->_spec->ds;
		
    // Chargement de l'utilisateur courant
    $currUser = new CMediusers();
    $currUser->load($user_id);
    
    $aidesUser = array();
    $aidesFunc = array();
    $order = "name";
		
    // Where user_id
    $whereUser = array();
    $whereUser["user_id"] = $ds->prepare("= %", $user_id);
    $whereUser["class"]   = $ds->prepare("= %", $compte_rendu->_class_name);
		
    // Where function_id
    $whereFunc = array();
    $whereFunc["function_id"] = $ds->prepare("= %", $currUser->function_id);
    $whereFunc["class"]       = $ds->prepare("= %", $compte_rendu->_class_name);
    
    // Chargement des aides
    $aide = new CAideSaisie();
    $aidesUser = $aide->loadList($whereUser,$order);
    $aidesFunc = $aide->loadList($whereFunc,$order);
    
    $this->helpers["Aide de l'utilisateur"] = "";
    foreach($aidesUser as $aideUser){
      if($aideUser->depend_value_1 == $modeleType){
        $this->helpers[$aideUser->name] = $aideUser->text;
      }
    }
    $this->helpers["Aide de la fonction"] = "";
    foreach($aidesFunc as $aideFunc){
      if($aideFunc->depend_value_1 == $modeleType){
        $this->helpers[$aideFunc->name] = $aideFunc->text;
      } 
    }
  }
  
  private function getBarcodeDataUri($code, $options) {
    if (!$code) return;
    
    $size = "{$options['width']}x{$options['width']}";
    
    if (isset(self::$barcodeCache[$code][$size])) {
      return self::$barcodeCache[$code][$size];
    }
    
    CAppUI::requireLibraryFile("tcpdf/barcode/barcode");
    CAppUI::requireLibraryFile("tcpdf/barcode/c128bobject");
    CAppUI::requireLibraryFile("tcpdf/barcode/cmb128bobject");
    
    $bc_options = (BCD_DEFAULT_STYLE | BCS_DRAW_TEXT) & ~BCS_BORDER;
    $barcode = new CMb128BObject($options["width"] * 2, $options["height"] * 2, $bc_options, $code);
    
    $barcode->SetFont(7);
    $barcode->DrawObject(2);
    
    ob_start();
    $barcode->FlushObject();
    $image = ob_get_contents();
    ob_end_clean();
    
    $barcode->DestroyObject();
    
    $image = "data:image/png;base64,".urlencode(base64_encode($image));
    
    return self::$barcodeCache[$code][$size] = $image;
  }
  
  function renderDocument($_source) {
    $fields = array();
    $values = array();
    
    foreach($this->sections as $properties) {
      foreach($properties as $property) {
        if ($property["valueHTML"] && isset($property["options"]["barcode"])) {
          $options = $property["options"]["barcode"];
          
          $image = $this->getBarcodeDataUri($property["valueHTML"], $options);
          
          $fields[] = "src=\"{$property['fieldHTML']}\"";
          $values[] = "src=\"$image\"";
        }
        else {
          $fields[] = $property["fieldHTML"];
          $values[] = nl2br($property["valueHTML"]);
        }
      }
    }
	
    if (count($fields)) {
      $this->document = str_ireplace($fields, $values, $_source);
    }
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
  
  // Vérification s'il s'agit d'un courrier
  function isCourrier() {
    $pos = strpos($this->document, "[Courrier -");
    if($pos) {
      $this->isCourrier = true;
    }
    return $this->isCourrier;
  }
}
?>