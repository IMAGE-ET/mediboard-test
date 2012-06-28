<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

class CTemplateManager {
  var $editor = "ckeditor";
  
  var $sections = array();
  var $helpers = array();
  var $lists = array();
  var $graphs = array();
  var $textes_libres = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  var $isCourrier = null;
  
  var $valueMode = true; // @todo : changer en applyMode
  var $isModele  = true;
  var $printMode = false;
  var $simplifyMode = false;
  var $parameters = array();
  
  var $destinataires = array();
  
  private static $barcodeCache = array();
  
  function CTemplateManager($parameters = array()) {
    $user = CMediusers::get();
    $this->parameters = $parameters;
    
    if (!isset($parameters["isBody"]) || (isset($parameters["isBody"]) && $parameters["isBody"] == 1)) {
      $this->addProperty("Courrier - nom destinataire"     , "[Courrier - nom destinataire]");
      $this->addProperty("Courrier - adresse destinataire" , "[Courrier - adresse destinataire]");
      $this->addProperty("Courrier - cp ville destinataire", "[Courrier - cp ville destinataire]");
      $this->addProperty("Courrier - copie à - simple"     , "[Courrier - copie à - simple]");
      $this->addProperty("Courrier - copie à - simple (multiligne)", "[Courrier - copie à - simple (multiligne)]");
      $this->addProperty("Courrier - copie à - complet", "[Courrier - copie à - complet]");
      $this->addProperty("Courrier - copie à - complet (multiligne)", "[Courrier - copie à - complet (multiligne)]");
    }
    
    $now = mbDateTime();
    $this->addDateProperty("Général - date du jour", $now);
    $this->addLongDateProperty("Général - date du jour (longue)", $now);
    $this->addTimeProperty("Général - heure courante", $now);
    
    // Connected user
    $user_complete = $user->_view;
    if ($user->isPraticien()) {
      if ($user->titres) {
        $user_complete .= "\n" . $user->titres;
      }
      if ($user->spec_cpam_id) {
        $spec_cpam = $user->loadRefSpecCPAM();
        $user_complete .= "\n" . $spec_cpam->text;
      }
      if ($user->adeli) {
        $user_complete .= "\nAdeli : " . $user->adeli;
      }
      if ($user->rpps) {
        $user_complete .= "\nRPPS : " . $user->rpps;
      }
      if ($user->_user_email) {
        $user_complete .= "\nE-mail : " . $user->_user_email;
      }
    }
    
    // Initials
    $elements_first_name = split("[ -]", $user->_user_first_name);
    $initials_first_name = "";
    
    foreach ($elements_first_name as $_element) {
      $initials_first_name .= strtoupper(substr($_element, 0, 1));
    }
    
    $elements_last_name = split("[ -]", $user->_user_last_name);
    $initials_last_name = "";
    
    foreach ($elements_last_name as $_element) {
      $initials_last_name .= strtoupper(substr($_element, 0, 1));
    }
    
    $this->addProperty("Général - rédacteur"        , $user->_shortview);
    $this->addProperty("Général - rédacteur - prénom", $user->_user_first_name);
    $this->addProperty("Général - rédacteur - nom"  , $user->_user_last_name);
    $this->addProperty("Général - rédacteur complet", $user_complete);
    $this->addProperty("Général - rédacteur (initiales) - prénom", $initials_first_name);
    $this->addProperty("Général - rédacteur (initiales) - nom", $initials_last_name);
    if (CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") && CAppUI::pref("pdf_and_thumbs")) {
      $this->addProperty("Général - numéro de page", "[Général - numéro de page]");
    }
  }
  
  function getParameter($name, $default = null) {
    return CValue::read($this->parameters, $name, $default);
  }

  function makeSpan($spanClass, $text) {
    // Escape entities cuz CKEditor does so
    $text = htmlentities($text);
    
    // Keep backslashed double quotes instead of quotes 
    // cuz CKEditor creates double quoted attributes
    return "<span class=\"{$spanClass}\">{$text}</span>";
  }
  
  function addProperty($field, $value = null, $options = array(), $htmlescape = true) {
    if ($htmlescape)
      $value = htmlspecialchars($value);
    
    $sec = explode(' - ', $field, 3);
    switch(count($sec)) {
      case 3:
        $section  = $sec[0];
        $item     = $sec[1];
        $sub_item = $sec[2];
        break;
      case 2:
        $section  = $sec[0];
        $item     = $sec[1];
        $sub_item = '';
        break;
      default:
        trigger_error("Error while exploding the string", E_USER_ERROR);
        return;
    }
    
    if (!array_key_exists($section, $this->sections)) {
      $this->sections[$section] = array();
    }
    if ($sub_item != '' && !array_key_exists($item, $this->sections[$section])) {
      $this->sections[$section][$item] = array();
    }
    
    if ($sub_item == '') {
      $this->sections[$section][$field] = array (
        "view"      => htmlentities($item),
        "field"     => $field,
        "value"     => $value,
        "fieldHTML" => htmlentities("[{$field}]"),
        "valueHTML" => $value,
        "shortview" => $section . " - " . $item,
        "options"   => $options
      );
    }
    else {
      $this->sections[$section][$item][$sub_item] = array (
        "view"      => htmlentities($sub_item),
        "field"     => $field,
        "value"     => $value,
        "fieldHTML" => htmlentities("[{$field}]"),
        "valueHTML" => $value,
        "shortview" => $section . " - " . $item . " - " . $sub_item,
        "options"   => $options
      );
    }
    
    if (isset($options["barcode"])) {
      $_field = &$this->sections[$section][$field];
      
      if ($this->valueMode)
        $src = $this->getBarcodeDataUri($_field['value'], $options["barcode"]);
      else 
        $src = $_field['fieldHTML'];
      
      $_field["field"] = "";
      
      if ($options["barcode"]["title"]) {
        $_field["field"] .= $options["barcode"]["title"]."<br />";
      }
      
      $_field["field"] .= "<img alt=\"$field\" src=\"$src\" ";
      
      foreach($options["barcode"] as $name => $attribute) {
        $_field["field"] .= " $name=\"$attribute\"";
      }
      
      $_field["field"] .= "/>";
    }
    
    if (isset($options["image"])) {
      
      $_field = &$this->sections[$section][$field];
      $src = $this->valueMode ? "?m=files&a=fileviewer&a=fileviewer&suppressHeaders=1&file_id=".$_field['value']."&phpThumb=1" : $_field['fieldHTML'];
      
      $_field["field"] = "<img src=\"$src\" />";
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
  
  function addListProperty($field, $items = null) {
    $this->addProperty($field, $this->makeList($items), null, false);
  }
  
  function addImageProperty($field, $file_id) {
    $this->addProperty($field, $file_id, array("image" => 1), false);
  }
  
  function makeList($items) {
    if (!$items) {
      return;
    }

    // Make a list out of a string
    if (!is_array($items)) {
      $items = array($items);
    }
    
    // Escape content
    $items = array_map("htmlentities", $items);
    
    // HTML production
    switch ($default = CAppUI::pref("listDefault")) {
      case "ulli":
        $html = "<ul>";
        foreach ($items as $item) {
          $html .= "<li>$item</li>";
        }
        $html.= "</ul>";
        break;
      
      case "br":
        $html = "";
        $prefix = CAppUI::pref("listBrPrefix");
        foreach ($items as $item) {
          $html .= "<br />$prefix $item";
        }
        break;
        
      case "inline":
        // Hack: obligé de décoder car dans ce mode le template manager 
        // le fera une seconde fois s'il ne détecte pas d'entités HTML
        $items = array_map("html_entity_decode", $items);
        $html = "";
        $separator = CAppUI::pref("listInlineSeparator");
        $html = implode(" $separator ", $items);
        break;
          
      default: 
        $html = "";
        trigger_error("Default style for list is unknown '$default'", E_USER_WARNING);
        break;
    }  
  
    return $html;
  }
  
  function addGraph($field, $data, $options = array()) {
    $this->graphs[utf8_encode($field)] = array(
      "data" => $data, 
      "options" => $options, 
      "name" => utf8_encode($field)
    );
    
    $this->addProperty($field, $field, null, false);
  }
  
  function addBarcode($field, $data, $options = array()) {
    $options = array_replace_recursive(array(
      "barcode" => array(
        "width"  => 220,
        "height" => 60,
        "class"  => "barcode",
        "title"  => "",
      )
    ), $options);
    
    $this->addProperty($field, $data, $options, false);
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
    $user = CMediusers::get($user_id);
    $user->loadRefFunction();
    if($user_id){
      $where[] = "(
        user_id = '$user->user_id' OR 
        function_id = '$user->function_id' OR 
        group_id = '{$user->_ref_function->group_id}'
      )";
    }
    else {
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
    $whereUser["class"]   = $ds->prepare("= %", $compte_rendu->_class);
    
    // Where function_id
    $whereFunc = array();
    $whereFunc["function_id"] = $ds->prepare("= %", $currUser->function_id);
    $whereFunc["class"]       = $ds->prepare("= %", $compte_rendu->_class);
    
    // Where group_id
    $whereGroup = array();
    $group = CGroups::loadCurrent();
    $whereGroup["group_id"] = $ds->prepare("= %", $group->_id);
    $whereGroup["class"]       = $ds->prepare("= %", $compte_rendu->_class);
    
    // Chargement des aides
    $aide = new CAideSaisie();
    $aidesUser   = $aide->loadList($whereUser,$order);
    $aidesFunc   = $aide->loadList($whereFunc,$order);
    $aidesGroup  = $aide->loadList($whereGroup,$order);

    $this->helpers["Aide de l'utilisateur"] = array();
    foreach($aidesUser as $aideUser){
      if($aideUser->depend_value_1 == $modeleType || $aideUser->depend_value_1 == ""){
        $this->helpers["Aide de l'utilisateur"][htmlentities($aideUser->name)] = htmlentities($aideUser->text);
      }
    }
    $this->helpers["Aide de la fonction"] = array();
    foreach($aidesFunc as $aideFunc){
      if($aideFunc->depend_value_1 == $modeleType || $aideFunc->depend_value_1 == ""){
        $this->helpers["Aide de la fonction"][htmlentities($aideFunc->name)] = htmlentities($aideFunc->text);
      } 
    }
    $this->helpers["Aide de l'&eacute;tablissement"] = array();
    foreach($aidesGroup as $aideGroup){
      if($aideGroup->depend_value_1 == $modeleType || $aideGroup->depend_value_1 == ""){
        $this->helpers["Aide de l'&eacute;tablissement"][htmlentities($aideGroup->name)] = htmlentities($aideGroup->text);
      } 
    }
  }
  
  function getBarcodeDataUri($code, $options) {
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
      foreach($properties as $key=>$property) {
        if (strpos($key, ' - ') === false) {
          foreach($property as $_property) {
            $fields[] = $_property["fieldHTML"];
            $values[] = nl2br($_property["valueHTML"]);
          }
        }
        else if ($property["valueHTML"] && isset($property["options"]["barcode"])) {
          $options = $property["options"]["barcode"];
          
          $image = $this->getBarcodeDataUri($property["valueHTML"], $options);
          
          $fields[] = "src=\"{$property['fieldHTML']}\"";
          $values[] = "src=\"$image\"";
        }
        else if ($property["valueHTML"] && isset($property["options"]["image"])) {
          $src = "?m=files&a=fileviewer&a=fileviewer&suppressHeaders=1&file_id=".$property['value']."&phpThumb=1";
          $fields[] = "src=\"{$property['fieldHTML']}\"";
          $values[] = "src=\"$src\"";
        }
        else {
          $property["fieldHTML"] = preg_replace("/'/",'&#39;', $property["fieldHTML"]);
          $fields[] = $property["fieldHTML"];
          $values[] =  nl2br($property["valueHTML"]);
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
      
      // Remplacer 039 par 39 car ckeditor remplace ' par &#39;
      $nom = str_replace("#039;", "#39;", htmlentities(stripslashes("[Liste - $value->nom]"),ENT_QUOTES));
      $pos = strpos($this->document, $nom);
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