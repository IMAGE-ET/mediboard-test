<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSmartyDP extends CSmartyMB {
  
  public static $placeholders = null;
  
  /**
   * Staticly build template placeholders array
   * @return void
   */
  public static final function makePlaceholders() {
    if (is_array(self::$placeholders)) {
      return;
    }
    
    // Static initialisations
    self::$placeholders = array();
    foreach (CAppUI::conf("template_placeholders") as $placeholder => $active) {
      if ($active) {
        self::$placeholders[$placeholder] = new $placeholder;
      }
    }
  }
  
  /**
   * Constructor
   */
  function __construct($dir = null) {
    parent::__construct($dir);

    $this->makePlaceholders();
    $this->assign("placeholders", self::$placeholders);
    
    $this->register_compiler_function("mb_return", array($this,"mb_return"));
    
    $this->register_block("mb_form" , array($this,"mb_form")); 
    $this->register_block("vertical", array($this,"vertical"));
    
    $this->register_function("mb_field"          , array($this,"mb_field"));
    $this->register_function("mb_key"            , array($this,"mb_key"));
    $this->register_function("mb_label"          , array($this,"mb_label"));
    $this->register_function("mb_title"          , array($this,"mb_title"));
    $this->register_function("mb_ternary"        , array($this,"mb_ternary"));
    $this->register_function("mb_colonne"        , array($this,"mb_colonne"));
  }
  
  /**
   * mb_return
   */
  function mb_return($tag_arg, &$smarty) {
    return "\nreturn;";
  }
  
  /**
   * mb_form
   */
  function mb_form($params, $content, &$smarty, &$repeat){
      $fields = array(
        "m"     => CMbArray::extract($params, "m", null, true),
        "dosql" => CMbArray::extract($params, "dosql"),
        "tab"   => CMbArray::extract($params, "tab"),
        "a"     => CMbArray::extract($params, "a"),
      );
      
      $attributes = array(
        "name"   => CMbArray::extract($params, "name", null, true),
        "method" => CMbArray::extract($params, "method", "get"),
        "action" => CMbArray::extract($params, "action", "?"),
        "class"  => CMbArray::extract($params, "className", ""),
      );
      
      $attributes += $params;
      
      $fields = array_filter($fields);
      
      $_content = "";
      foreach($fields as $name => $value) {
        $_content .= "\n".CHTMLResourceLoader::getTag("input", array(
          "type"  => "hidden",
          "name"  => $name,
          "value" => $value,
        ));
      }
      
      $_content .= $content;
      
      return CHTMLResourceLoader::getTag("form", $attributes, $_content);
    }

   /*
   * Diplays veritcal text
   */
    function vertical($params, $content, &$smarty, &$repeat) {
      if (isset($content)) {
        $content = trim($content);
        $content = preg_replace("/\s+/", " ", $content);
        $orig = $content;
        $content = strip_tags($content);
        $content = preg_replace("/\s+/", chr(0xA0), $content); // == nbsp
        
        $letters = str_split($content);
        
        $html = "";
        foreach($letters as $_letter) {
          $html .= "<i>$_letter</i>";
        }
        
        return "<span class=\"vertical\"><span class=\"nowm\">$html</span><span class=\"orig\">$orig</span></span>";
      }
    }

   
  
   /**
   * @param array params tableau des parametres
   * - object          : Objet
   * - field           : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
   * - prop            : {optionnel} Specification du champs, par defaut, celle de la classe
   * - separator       : {optionnel} Séparation entre les champs de type "radio" [default: ""]
   * - cycle           : {optionnel} Cycle de répétition du séparateur (pour les enums en type radio) [default: "1"]
   * - typeEnum        : {optionnel} Type d'affichage des enums (values : "select", "radio") [default: "select"]
   * - emptyLabel      : {optionnel} Ajout d'un "option" en amont des valeurs ayant pour value ""
   * - class           : {optionnel} Permet de donner une classe aux champs
   * - hidden          : {optionnel} Permet de forcer le type "hidden"
   * - canNull         : {optionnel} Permet de passer outre le notNull de la spécification
   */
  function mb_field($params, &$smarty) {
    if (CAppUI::conf("readonly")) {
      //$params["readonly"] = 1;
    }
    
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
  
    if (null == $object = CMbArray::extract($params, "object")) {
      $class = CMbArray::extract($params, "class" , null, true);
      $object = new $class;
    }
    
    $field   = CMbArray::extract($params, "field" , null, true);
    $prop    = CMbArray::extract($params, "prop");
    $canNull = CMbArray::extract($params, "canNull");
    
    if (null !== $value = CMbArray::extract($params, "value")) {
      $object->$field = $value;
    }
      
    // Get spec, may create it
    $spec = $prop !== null ? 
      CMbFieldSpecFact::getSpec($object, $field, $prop) : 
      $object->_specs[$field];
    
    if ($canNull === "true" || $canNull === true) {
      $spec->notNull = 0;
      $tabSpec = explode(" ",$spec->prop);
      CMbArray::removeValue("notNull", $tabSpec);
      $spec->prop = implode(" ", $tabSpec);
    }
    
    if ($canNull === "false" || $canNull === false) {
      $spec->notNull = 1;
      $spec->prop = "canNull notNull $spec->prop";
    }
  
    return $spec->getFormElement($object, $params);
  }
  
  /**
   * Fonction d'écriture  des labels
   * @param array params tableau des parametres
   * - object      : Objet
   * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
   * - defaultFor  : {optionnel} Ajout d'une valeur à cibler pour "select" ou "radio"
   * - typeEnum    : {optionnel} Type d'affichage des enums à cibler (values : "select", "radio") [default: "select"]
   */
  function mb_label($params, &$smarty) {
    if (null == $object = CMbArray::extract($params, "object")) {
      $class = CMbArray::extract($params, "class" , null, true);
      $object = new $class;
    }
    
    $field = CMbArray::extract($params, "field" , null, true);
    
    if (!array_key_exists($field, $object->_specs)) {
       $object->_specs[$field] = CMbFieldSpecFact::getSpec($object, $field, "");
       trigger_error("Spec missing for class '$object->_class' field '$field'", E_USER_WARNING);
    }
  
    return $object->_specs[$field]->getLabelElement($object, $params);
  }
  
  /**
   * Fonction d'écriture  des labels de titre
   * @param array params tableau des parametres
   * - object      : Objet
   * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
   */
  function mb_title($params, &$smarty) {
    if (null == $object = CMbArray::extract($params, "object")) {
      $class = CMbArray::extract($params, "class" , null, true);
      $object = new $class;
    }
    
    $field = CMbArray::extract($params, "field" , null, true);
  
    return $object->_specs[$field]->getTitleElement($object, $params);
  }
  
  /**
   * Fonction d'écriture  des labels
   * @param array params 
   * - var   : Name of the new variable
   * - test  : Test for ternary operator 
   * - value : Value if test is true
   * - other : Value if test is false
   */
  function mb_ternary($params, &$smarty) {
    $test  = CMbArray::extract($params, "test"  , null, true);
    $value = CMbArray::extract($params, "value" , null, true);
    $other = CMbArray::extract($params, "other" , null, true);
    
    $result =  $test ? $value : $other;
    
    if ($var = CMbArray::extract($params, "var", null)) {
      $smarty->assign($var, $result);
    }
    else {
      return $result;
    }
  }
  
  function mb_colonne($params, &$smarty) {
    $class         = CMbArray::extract($params, "class"        , null, true);
    $field         = CMbArray::extract($params, "field"        , null, true);
    $order_col     = CMbArray::extract($params, "order_col"    , null, true);
    $order_way     = CMbArray::extract($params, "order_way"    , null, true);
    $order_suffixe = CMbArray::extract($params, "order_suffixe", ""  , false);
    $url           = CMbArray::extract($params, "url"          , null, false);
    $function      = CMbArray::extract($params, "function"     , null, false);
    
    $sHtml  = "<label for=\"$field\" title=\"".CAppUI::tr("$class-$field-desc")."\">";
    $sHtml .= CAppUI::tr("$class-$field-court");
    $sHtml .= "</label>";
      
    $css_class = ($order_col == $field) ? "sorted" : "sortable";
    $order_way_inv = ($order_way == "ASC") ? "DESC" : "ASC";
    
    if($url){
      if($css_class == "sorted"){
        return "<a class='$css_class $order_way' href='$url&amp;order_col$order_suffixe=$order_col&amp;order_way$order_suffixe=$order_way_inv'>$sHtml</a>";
      }
      if($css_class == "sortable"){
        return "<a class='$css_class' href='$url&amp;order_col$order_suffixe=$field&amp;order_way$order_suffixe=ASC'>$sHtml</a>";
      }
    }
    
    if($function){
      if($css_class == "sorted"){
        return "<a class='$css_class $order_way' onclick=$function('$order_col','$order_way_inv');>$sHtml</a>";
      }
      if($css_class == "sortable"){
        return "<a class='$css_class' onclick=$function('$field','ASC');>$sHtml</a>";
      }    
    }
  }
    
}