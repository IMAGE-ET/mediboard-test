<?php /* $Id$ */
  
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass('dPfiles', 'documentItem');

class CCompteRendu extends CDocumentItem {
  // DB Table key
  var $compte_rendu_id   = null;
  
  // DB References
  var $chir_id           = null; // not null when is a template associated to a user
  var $function_id       = null; // not null when is a template associated to a function
  var $group_id          = null; // not null when is a template associated to a group
  var $content_id        = null;
  
  // DB fields
  var $nom               = null;
  var $type              = null;
  var $valide            = null;
  var $header_id         = null;
  var $footer_id         = null;
  var $height            = null;
  var $margin_top        = null;
  var $margin_bottom     = null;
  var $margin_left       = null;
  var $margin_right      = null;
  var $page_height       = null;
  var $page_width        = null;
  var $private           = null;
  var $fast_edit         = null;

  /// Form fields
  var $_is_document      = false;
  var $_is_modele        = false;
  var $_owner            = null;
  var $_page_format      = null;
  var $_orientation      = null;
  var $_list_classes     = null;

  // Distant field
  var $_source           = null;
  
  // Referenced objects
  var $_ref_chir         = null;
  var $_ref_category     = null;
  var $_ref_function     = null;
  var $_ref_group        = null;
  var $_ref_header       = null;
  var $_ref_footer       = null;
  var $_ref_file         = null;
	var $_ref_content      = null;

  static $_page_formats = array(
    'a3'      => array(29.7 , 42),
    'a4'      => array(21   , 29.7),
    'a5'      => array(14.8 , 21),
    'letter'  => array(21.6 , 27.9),
    'legal'   => array(21.6 , 35.6),
    'tabloid' => array(27.9 , 43.2),
  );
  
  static $templated_classes = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'compte_rendu';
    $spec->key   = 'compte_rendu_id';
    $spec->measureable = true;
    $spec->xor["owner"] = array("chir_id", "function_id", "group_id", "object_id");
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["listes_choix"] = "CListeChoix compte_rendu_id";
    $backProps["modeles_headed"] = "CCompteRendu header_id";
    $backProps["modeles_footed"] = "CCompteRendu footer_id";
    return $backProps;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["chir_id"]          = "ref class|CMediusers purgeable";
    $specs["function_id"]      = "ref class|CFunctions purgeable";
    $specs["group_id"]         = "ref class|CGroups purgeable";
    $specs["object_id"]        = "ref class|CMbObject meta|object_class purgeable";
		$specs["content_id"]       = "ref class|CContenthtml show|0";
    $specs["object_class"]     = "str notNull class show|0";
    $specs["nom"]              = "str notNull show|0 seekable";
    $specs["type"]             = "enum list|header|body|footer default|body";
    $specs["_list_classes"]    = "enum list|CBloodSalvage|CConsultAnesth|CConsultation|CDossierMedical|CFunctions|CGroups|CMediusers|COperation|CPatient|CPrescription|CSejour";
    //mbTrace(implode("|", array_keys(CCompteRendu::getTemplatedClasses())));
    $specs["header_id"]        = "ref class|CCompteRendu";
    $specs["footer_id"]        = "ref class|CCompteRendu";
    $specs["height"]           = "float min|0";
    $specs["margin_top"]       = "float notNull min|0 default|2 show|0";
    $specs["margin_bottom"]    = "float notNull min|0 default|2 show|0";
    $specs["margin_left"]      = "float notNull min|0 default|2 show|0";
    $specs["margin_right"]     = "float notNull min|0 default|2 show|0";
    $specs["page_height"]      = "float notNull min|1 default|29.7 show|0";
    $specs["page_width"]       = "float notNull min|1 default|21 show|0";
    $specs["valide"]           = "bool";
    $specs["private"]          = "bool notNull default|0";
    $specs["fast_edit"]        = "bool notNull default|0";
    $specs["_owner"]           = "enum list|prat|func|etab";
    $specs["_orientation"]     = "enum list|portrait|landscape";
    $specs["_page_format"]     = "enum list|".implode("|", array_keys(self::$_page_formats));
    $specs["_source"]          = "html";
    return $specs;
  }
  
  function getContent() {
    $this->loadContent();
		return $this->_source;
  }
  
  function loadModeles($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!isset($where["object_id"])) {
      $where["object_id"] = "IS NULL";
    }

    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }

  function loadDocuments($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    global $can;
    if (!isset($where["object_id"])) {
      $where["object_id"] = "IS NOT NULL";
    }
    
    $docs = parent::loadList($where, $order, $limit, $group, $leftjoin);
    $current_user = CAppUI::$user;
    $current_user->loadRefFunction();

    foreach($docs as $_doc) {
      if(!$docs[$key]->canRead()){
        unset($docs[$_doc->_id]);
      }
    }
    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_extensioned = "$this->nom.htm";
    $this->_view = $this->object_id ? "" : "Modèle : ";
    $this->_view.= $this->nom;
    
    if ($this->chir_id    ) $this->_owner = "prat";
    if ($this->function_id) $this->_owner = "func";
    if ($this->group_id   ) $this->_owner = "etab";

    $this->_page_format = "";
    
    foreach(CCompteRendu::$_page_formats as $_key=>$_format) {
      if(($_format[0] == $this->page_width && $_format[1] == $this->page_height) ||
        ($_format[1] == $this->page_width && $_format[0] == $this->page_height)) {
        $this->_page_format = $_key;
        break;
      }
    }
    
    $this->_orientation = "portrait";
    
    if ($this->page_width > $this->page_height) {
      $this->_orientation = "landscape";
    }
  }

  function updateDBFields() {
    parent::updateDBFields();
    
		// Valeur par défaut pour private
    $this->completeField("private");
    if($this->private === "") {
      $this->private = 0;
    }
  }
  
	function loadContent($store_source = true) {
		$this->_ref_content = $this->loadFwdRef("content_id", true);
		if ($store_source) {
		  $this->_source = $this->_ref_content->content;
		}
	}
	
  function loadComponents() {
    $this->_ref_header = $this->loadFwdRef("header_id", true);
    $this->_ref_footer = $this->loadFwdRef("footer_id", true);
  }

  function loadFile() {
    if ($this->_id) {
      return;
    }
   
  	$this->_ref_file = new CFile;
    $this->_ref_file->setObject($this);
    $this->_ref_file->loadMatchingObject();
  }
	
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->_ref_object->loadRefsFwd();
    
    // Chirurgien
    $this->_ref_chir = new CMediusers;
    if($this->chir_id) {
      $this->_ref_chir->load($this->chir_id);
    } 
    elseif($this->object_id) {
      switch($this->object_class) {
        case "CConsultation" :
          $this->_ref_chir->load($this->_ref_object->_ref_plageconsult->chir_id);
          break;
        case "CConsultAnesth" :
          $this->_ref_object->_ref_consultation->loadRefsFwd();
          $this->_ref_chir->load($this->_ref_object->_ref_consultation->_ref_plageconsult->chir_id);
          break;
        case "COperation" :
          $this->_ref_chir->load($this->_ref_object->chir_id);
          break;
      }
    }

    // Fonction
    $this->_ref_function = new CFunctions;
    if($this->function_id)
      $this->_ref_function->load($this->function_id);
      
    // Etablissement
    $this->_ref_group = new CGroups();
    if($this->group_id)
      $this->_ref_group->load($this->group_id);
  }
  
  static function loadModeleByCat($catName, $where1 = null, $order = "nom", $horsCat = null){
    $ds = CSQLDataSource::get("std");
    $where = array();
    if(is_array($catName)) {
      $where = array_merge($where, $catName);
    }elseif(is_string($catName)){
      $where["nom"] = $ds->prepare("= %", $catName);
    }
    $category = new CFilesCategory;
    $resultCategory = $category->loadList($where);
    $documents = array();
    
    if(count($resultCategory) || $horsCat){
      $where = array();
      if($horsCat){
        $resultCategory[0] = "";
        $where[] = "file_category_id IS NULL OR file_category_id ".CSQLDataSource::prepareIn(array_keys($resultCategory));
      } else {
        $where["file_category_id"] = CSQLDataSource::prepareIn(array_keys($resultCategory));
      }
      $where["object_id"] = " IS NULL";
      if($where1){
        if(is_array($where1)) {
          $where = array_merge($where, $where1);
        }elseif(is_string($where1)){
          $where[] = $where1;
        }
      }
      $resultDoc = new CCompteRendu;
      $documents = $resultDoc->loadList($where,$order);
    }
    return $documents;
  }
  
  /**
   * Charge tous les modèles pour une classe d'objets associés à un utilisateur
   * @param $prat_id ref|CMediuser L'utilisateur concerné
   * @param $object_class string Nom de la classe d'objet, optionnel. Doit être un CMbObject
   * @param $type enum list|header|body|footer Type de composant, optionnel
   * @return array ("prat" => array<CCompteRendu>, "func" => array<CCompteRendu>, "etab" => array<CCompteRendu>)
   */
  static function loadAllModelesFor($id, $owner = 'prat', $object_class = null, $type = null) {
    $modeles = array(
      "prat" => array(),
      "func" => array(),
      "etab" => array(),
    );
    
    if (!$id) return $modeles;
    
    // Clauses de recherche
    $modele = new CCompteRendu();
    $where = array();
    $where["object_id"] = "IS NULL";
    
    if ($object_class) {  
      $where["object_class"] = "= '$object_class'";
    }
    
    if ($type) {
      $where["type"] = "= '$type'";
    }
    
    $order = "object_class, type, nom";

    switch ($owner) {
      case 'prat': // Modèle du praticien
        $prat = new CMediusers();
        if (!$prat->load($id)) return $modeles;
        $prat->loadRefFunction();

        $where["chir_id"]     = "= '$prat->_id'";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = "IS NULL";
        $modeles["prat"] = $modele->loadlist($where, $order);
        
      case 'func': // Modèle de la fonction
        if (isset($prat)) {
          $func_id = $prat->function_id;
        } else {
          $func = new CFunctions();
          if (!$func->load($id)) return $modeles;
          
          $func_id = $func->_id;
        }
        
        $where["chir_id"]     = "IS NULL";
        $where["function_id"] = "= '$func_id'";
        $where["group_id"]    = "IS NULL";
        $modeles["func"] = $modele->loadlist($where, $order);
        
      case 'etab': // Modèle de l'établissement
        $etab_id = CGroups::loadCurrent()->_id;
        if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) return $modeles;
          
          $etab_id = $etab->_id;
        }
        else if (isset($func)) {
          $etab_id = $func->group_id;
        } 
        else if(isset($func_id)) {
          $func = new CFunctions();
          $func->load($func_id);
          
          $etab_id = $func->group_id;
        }
        
        $where["chir_id"]     = "IS NULL";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = " = '$etab_id'";
        $modeles["etab"] = $modele->loadlist($where, $order);
    }
    
    return $modeles;
  }
  
  function loadView() {
  	parent::loadView();
    $this->loadFile();
  }
    
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function || $this->_ref_group) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }
    if($this->_ref_object->_id){
      $can = $this->_ref_object->getPerm($permType);
    }
    elseif($this->_ref_chir->_id) {
      $can = $this->_ref_chir->getPerm($permType);
    }
    elseif($this->_ref_function->_id) {
      $can = $this->_ref_function->getPerm($permType);
    }
    else {
      $can = $this->_ref_group->getPerm($permType);
    }
    return $can;
  }
  
  function store() {
    $this->completeField("content_id", "_source");
    $this->loadContent(false);
		$source_modified = 
			$this->_ref_content->content != $this->_source || 
		  $this->fieldModified("margin_top") || 
		  $this->fieldModified("margin_left") || 
		  $this->fieldModified("margin_right") || 
		  $this->fieldModified("margin_bottom") || 
		  $this->fieldModified("page_height") || 
		  $this->fieldModified("page_width") || 
		  $this->fieldModified("header_id") || 
		  $this->fieldModified("footer_id");
	  
    if ($source_modified) {
    	// Empty PDF File
      foreach($this->loadBackRefs("files") as $_file) {
        $_file->file_empty();
      }
			
			// Send status to obsolete
			$this->completeField("etat_envoi");
      if ($source_modified && $this->etat_envoi == "oui") {
        $this->etat_envoi = "obsolete";
      }
    }

    $this->_ref_content->content = $this->_source;

    // Si c'est un entête ou pied, et utilisé dans des documents dont le type ne correspond pas au nouveau
    // alors pas d'enregistrement
    if (in_array($this->type, array("footer", "header"))) {
      $doc = new CCompteRendu;
      $where = 'object_class != "'. $this->object_class.
          '" and ( header_id ="' . $this->_id .
          '" or footer_id ="' . $this->_id . '")';;
      if ($doc->countList($where))
        return "Impossible d'enregistrer, car des documents sont rattachés à ce pied de page (ou entête) et ils ont un type différent";
    }
    // Si c'est un document dont le type de l'en-tête ou du pied de page ne correspond pas à son nouveau type
    // alors pas d'enregistrement
    if ($this->header_id) {
      $header = new CCompteRendu;
      $header->load($this->header_id);
      if ($header->object_class != $this->object_class) {
        return "Impossible de sauvegarder, le document n'est pas du même type que son entête";
      }
    }
    
    if ($this->footer_id) {
      $header = new CCompteRendu;
      $header->load($this->footer_id);
      if ($header->object_class != $this->object_class) {
        return "Impossible de sauvegarder, le document n'est pas du même type que son pied de page";
      }
    }

    $this->_ref_content->store();

    if (!$this->content_id )
      $this->content_id = $this->_ref_content->_id;

    return parent::store();
  }
	
  function delete() {
    $this->completeField("content_id");
    $this->loadContent(false);
    $file = new CFile();
    $files = $file->loadFilesForObject($this);
    
    foreach($files as $_file) {
      $_file->delete();
    }
    $this->_ref_content->delete();
    return parent::delete();
  }
	
  function handleSend() {
    if (!$this->_send) {
      return;
    }

    $this->completeField("nom", "_source");
    
    return parent::handleSend();
  }
	
  static function getTemplatedClasses() {
    if (self::$templated_classes !== null) {
      return self::$templated_classes;
    }
    
    $classes = array();
    
    $installed = getInstalledClasses();
    foreach ($installed as $key=>$class) {
      if (is_method_overridden($class, 'fillTemplate') || is_method_overridden($class, 'fillLimitedTemplate')) {
        $classes[$class] = CAppUI::tr($class);
      }
    }
    
    return self::$templated_classes = $classes;
  }

  function loadHTMLcontent($htmlcontent, $mode = "modele", $type = "body", $header = "", $sizeheader = 0, $footer = "", $sizefooter = 0, $margins = array()) {
    $style = file_get_contents("style/mediboard/htmlarea.css") .
      "@page {
         margin-top:    {$margins[0]}cm;
         margin-right:  {$margins[1]}cm;
         margin-bottom: {$margins[2]}cm;
         margin-left:   {$margins[3]}cm;
       }
       body {
         margin:  0;
         padding: 0;
       }";

    $content = "";
    $position = array(
      "header" => "top",
      "footer" => "bottom"
    );
                      
    if($mode == "modele") {
      switch($type) {
        case "header":
        case "footer":
          $position = $position[$type];
          $sizeheader = $sizeheader != '' ? $sizeheader : 50;
          $hauteur_position = 0;
          
          $style .= "
            #{$type} {
              height: {$sizeheader}px;
              {$position}: 0cm;
              width: auto;
            }";
          
          $content =  "<div id=\"$type\">$htmlcontent</div>";
          break;
        case "body":
          if($header) {
            $sizeheader = $sizeheader != '' ? $sizeheader : 50;
            $padding_top = $sizeheader;
            
            $style .= "
                @media print {
                  #body { 
                    padding-top: {$padding_top}px;
                  }
                  #header {
                    height: {$sizeheader}px;
                    top: 0cm;
                  }
                }";
              
            $content .= "<div id=\"header\">$header</div>";
          }
          if($footer) {
            $sizefooter = $sizefooter != '' ? $sizefooter : 50;
            $padding_bottom = $sizefooter;
            $style .= "
                @media print {
                  #body { 
                    padding-bottom: {$padding_bottom}px;
                  }
                  #footer {
                    height: {$sizefooter}px;
                    bottom: 0cm;
                  }
                }";
            $content .= "<div id=\"footer\">$footer</div>";
          }
          $content .= "<div id=\"body\">$htmlcontent</div>";
        }
    }
    else {
      $content = $htmlcontent;
    }
    $smarty = new CSmartyDP();
    $smarty->assign("style"  , $style);
    $smarty->assign("content", $content);
    return $smarty->fetch("../../dPcompteRendu/templates/htmlheader.tpl");
  }
  
  function generateDocFromModel() {
    $source = $this->_source;
    
    if ($this->header_id || $this->footer_id) {
      $this->loadComponents();
      
      $header = $this->_ref_header;
      $footer = $this->_ref_footer;
    }
    
    if (isset($header) || isset($footer)) {
      $header->height = isset($header->height) ? $header->height : 20;
      $footer->height = isset($footer->height) ? $footer->height : 20;
    
      $style = "
        <style type='text/css'>
        #header {
          height: {$header->height}px;
          /*DOMPDF top: 0;*/
        }
  
        #footer {
          height: {$footer->height}px;
          /*DOMPDF bottom: 0;*/
        }";
      
      if ($header->_id) {
        $header->loadContent();
        $header->_source = "<div id='header'>$header->_source</div>";
        
        if(CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 0) {      
          $header->height += 20;
        }
      }
    
      if ($footer->_id) {
        $footer->loadContent();
        $footer->_source = "<div id='footer'>$footer->_source</div>";
  
        if(CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 0) {
          $footer->height += 20;
        }
      }
    
      $style.= "
        @media print { 
          #body { 
            padding-top: {$header->height}px;
          }
          hr.pagebreak {
            padding-top: {$header->height}px;
          }
        }";
    
      $style .="
        @media dompdf {
          #body {
            padding-bottom: {$footer->height}px;
          }
          hr.pagebreak {
            padding-top: 0px;
          }
        }</style>";
      
      $source = "<div id='body'>$source</div>";
      $source = $style . $header->_source . $footer->_source . $source;
    }
    return $source;
  }
  
  function replaceFreeTextFields($source, $textes_libres) {
    $patterns = array();
    
    foreach ($textes_libres as $key=>$_texte) {
      $patterns[] = "/\[\[Texte libre - $key\]\]/i";
    }
  
    return preg_replace($patterns, $textes_libres, $source, 1);
  }
}

?>