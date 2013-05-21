<?php

/**
 * $Id$
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Gestion de documents / modèles avec marges, entêtes et pieds de pages.
 * Un modèle est associé à un utilisateur, une fonction ou un établissement.
 * Le document est une utilisation d'un modèle (référencé par modele_id)
 */
class CCompteRendu extends CDocumentItem {
  // DB Table key
  public $compte_rendu_id;

  // DB References
  public $user_id; // not null when is a template associated to a user
  public $function_id; // not null when is a template associated to a function
  public $group_id; // not null when is a template associated to a group
  public $content_id;
  public $header_id;
  public $footer_id;
  public $preface_id;
  public $ending_id;
  public $modele_id;

  // DB fields
  public $nom;
  public $type;
  public $font;
  public $size;
  public $valide;
  public $height;
  public $margin_top;
  public $margin_bottom;
  public $margin_left;
  public $margin_right;
  public $page_height;
  public $page_width;
  public $fast_edit;
  public $fast_edit_pdf;
  public $date_print;
  public $purge_field;
  public $purgeable;
  public $fields_missing;
  public $version;

  // Form fields
  public $_is_document      = false;
  public $_is_modele        = false;
  public $_is_locked        = false;
  public $_owner;
  public $_page_format;
  public $_orientation;
  public $_list_classes;
  public $_date;
  public $_count_utilisation;

  // Distant field
  public $_source;

  /** @var CMediusers */
  public $_ref_user;

  /** @var CMediusers */
  public $_ref_author;

  /** @var CFilesCategory */
  public $_ref_category;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CGroups */
  public $_ref_group;

  /** @var CCompteRendu */
  public $_ref_header;

  /** @var CCompteRendu */
  public $_ref_preface;

  /** @var CCompteRendu */
  public $_ref_ending;

  /** @var CCompteRendu */
  public $_ref_footer;

  /** @var CFile */
  public $_ref_file;

  /** @var CCompteRendu */
  public $_ref_modele;

  /** @var CContentHTML */
  public $_ref_content;

  /** @var CCorrespondantCourrier[] */
  public $_refs_correspondants_courrier;
  public $_refs_correspondants_courrier_by_tag_guid;

  // Other fields
  public $_entire_doc;
  public $_ids_corres;

  static $_page_formats = array(
    'a3'      => array(29.7 , 42),
    'a4'      => array(21   , 29.7),
    'a5'      => array(14.8 , 21),
    'letter'  => array(21.6 , 27.9),
    'legal'   => array(21.6 , 35.6),
    'tabloid' => array(27.9 , 43.2),
  );

  static $templated_classes = null;

  static $fonts = array(
    ""          => "", // empty font
    "arial"     => "Arial",
    "calibri"   => "Calibri",
    "comic"     => "Comic Sans MS",
    "courier"   => "Courier New",
    "georgia"   => "Georgia",
    "lucida"    => "Lucida Sans Unicode",
    "symbol"    => "Symbol",
    "tahoma"    => "Tahoma",
    "times"     => "Times New Roman",
    "trebuchet" => "Trebuchet MS",
    "verdana"   => "Verdana",
    "zapfdingbats" => "ZapfDingBats"
  );

  // Noms de modèles réservés
  static $special_names = array(
    "CConsultAnesth" => array(
      "[FICHE ANESTH]" => "body",
    ),
    "COperation" => array(
      "[FICHE DHE]" => "body",
    ),
    "CPrescription"  => array (
      "[ENTETE ORDONNANCE]"           => "header",
      "[PIED DE PAGE ORDONNANCE]"     => "footer",
      "[ENTETE ORDONNANCE ALD]"       => "header",
      "[PIED DE PAGE ORDONNANCE ALD]" => "footer",
      "[ENTETE BON]"                  => "header",
      "[PIED DE PAGE BON]"            => "footer",
    ),
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'compte_rendu';
    $spec->key   = 'compte_rendu_id';
    $spec->measureable = true;
    $spec->xor["owner"] = array("user_id", "function_id", "group_id", "object_id");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["listes_choix"]   = "CListeChoix compte_rendu_id";
    $backProps["modeles_headed"] = "CCompteRendu header_id";
    $backProps["modeles_footed"] = "CCompteRendu footer_id";
    $backProps["modeles_prefaced"] = "CCompteRendu preface_id";
    $backProps["modeles_ended"]  = "CCompteRendu ending_id";
    $backProps["documents_generated"] = "CCompteRendu modele_id";
    $backProps["pack_links"]     = "CModeleToPack modele_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier compte_rendu_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]          = "ref class|CMediusers purgeable show|1";
    $props["function_id"]      = "ref class|CFunctions purgeable";
    $props["group_id"]         = "ref class|CGroups purgeable";
    $props["object_id"]        = "ref class|CMbObject meta|object_class purgeable show|1";
    $props["content_id"]       = "ref class|CContentHTML show|0";
    $props["object_class"]     = "str notNull class show|0";
    $props["nom"]              = "str notNull show|0 seekable";
    $props["font"]             = "enum list|arial|calibri|comic|courier|georgia|lucida|symbol|".
                                 "tahoma|times|trebuchet|verdana|zapfdingbats show|0";
    $props["size"]             = "enum list|xx-small|x-small|small|medium|large|x-large|xx-large|".
                                 "8pt|9pt|10pt|11pt|12pt|14pt|16pt|18pt|20pt|22pt|24pt|26pt|28pt|36pt|48pt|72pt show|0";
    $props["type"]             = "enum list|header|preface|body|ending|footer default|body";
    $props["_list_classes"]    = "enum list|".implode("|", array_keys(CCompteRendu::getTemplatedClasses()));
    $props["header_id"]        = "ref class|CCompteRendu";
    $props["footer_id"]        = "ref class|CCompteRendu";
    $props["preface_id"]       = "ref class|CCompteRendu";
    $props["ending_id"]        = "ref class|CCompteRendu";
    $props["modele_id"]        = "ref class|CCompteRendu nullify show|0";
    $props["height"]           = "float min|0 show|0";
    $props["margin_top"]       = "float notNull min|0 default|2 show|0";
    $props["margin_bottom"]    = "float notNull min|0 default|2 show|0";
    $props["margin_left"]      = "float notNull min|0 default|2 show|0";
    $props["margin_right"]     = "float notNull min|0 default|2 show|0";
    $props["page_height"]      = "float notNull min|1 default|29.7 show|0";
    $props["page_width"]       = "float notNull min|1 default|21 show|0";
    $props["valide"]           = "bool show|0";
    $props["fast_edit"]        = "bool default|0 show|0";
    $props["fast_edit_pdf"]    = "bool default|0 show|0";
    $props["date_print"]       = "dateTime show|0";
    $props["purge_field"]      = "str show|0";
    $props["purgeable"]        = "bool default|0 show|0";
    $props["fields_missing"]   = "num default|0 show|0";
    $props["version"]          = "num default|0";
    $props["_owner"]           = "enum list|prat|func|etab";
    $props["_orientation"]     = "enum list|portrait|landscape";
    $props["_page_format"]     = "enum list|".implode("|", array_keys(self::$_page_formats));
    $props["_source"]          = "html helped|_list_classes";
    $props["_entire_doc"]      = "html";
    $props["_ids_corres"]      = "str";
    $props["_date"]            = "dateTime show|1";
    return $props;
  }

  /**
   * Génère et retourne le fichier PDF si possible,
   * la source html sinon.
   * 
   * @return string
   */
  function getBinaryContent() {
    // Content from PDF preview
    $this->makePDFpreview();
    $file = $this->_ref_file;
    if ($file->_id) {
      return $file->getBinaryContent();
    }

    // Or acutal HTML source
    $this->loadContent();
    return $this->_source;
  }

  /**
   * Retourne le nom du fichier associé
   * 
   * @return string 
   */
  function getExtensioned() {
    $file = $this->loadFile();
    if ($file->_id) {
      $this->_extensioned = $file->_extensioned;
    }
    return parent::getExtensioned();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_extensioned = "$this->nom.htm";
    $this->_view = $this->object_id ? "" : "Modèle : ";
    $this->_view.= $this->nom;

    $modele = $this->loadModele();

    if ($modele->_id && $modele->purgeable) {
      $this->_view = "[temp] " . $this->_view;
    }

    if ($this->object_id && $this->fields_missing) {
      $this->_view = " [" . $this->fields_missing . "] $this->_view";
    }

    if ($this->user_id) {
      $this->_owner = "prat";
    }
    if ($this->function_id) {
      $this->_owner = "func";
    }

    if ($this->group_id) {
      $this->_owner = "etab";
    }

    $this->_page_format = "";

    foreach (CCompteRendu::$_page_formats as $_key=>$_format) {
      if (($_format[0] == $this->page_width && $_format[1] == $this->page_height) ||
          ($_format[1] == $this->page_width && $_format[0] == $this->page_height)
      ) {
        $this->_page_format = $_key;
        break;
      }
    }

    // Formatage de la page  
    if (!$this->_page_format) {
      $page_width  = round((72 / 2.54) * $this->page_width, 2);
      $page_height = round((72 / 2.54) * $this->page_height, 2);
      $this->_page_format = array(0, 0, $page_width, $page_height);
    }

    $this->_orientation = "portrait";

    if ($this->page_width > $this->page_height) {
      $this->_orientation = "landscape";
    }
  }

  /**
   * Charge le contenu html
   * 
   * @param boolean $field_source [optional]
   * 
   * @return void
   */
  function loadContent($field_source = true) {
    $curr_user = CMediusers::get();

    $content = $this->loadFwdRef("content_id", true);;
    /** @var  CContentHTML $content */
    $this->_ref_content = $content;

    $this->_ref_content->content = preg_replace("/#body\s*{\s*padding/", "body { margin", $this->_ref_content->content);

    $days = CAppUI::conf("dPcompteRendu CCompteRendu days_to_lock");
    $days = isset($days[$this->object_class]) ?
      $days[$this->object_class] : $days["base"];

    $last_log = $this->_ref_content->loadLastLogForField("content");
    if (
        (CMbDT::daysRelative($last_log->date, CMbDT::dateTime()) > $days) ||
        (!CCAnDo::admin() && $this->valide && $this->author_id != $curr_user->_id)
    ) {
      $this->_is_locked = true;
    }

    if ($field_source) {
      $this->_source = $this->_ref_content->content;
      $this->_source = preg_replace("/<meta[^>]+>/", '', $this->_source);
      $this->_source = preg_replace("/<\/meta>/", '', $this->_source);

      // Suppression des commentaires, provenant souvent de Word
      $this->_source =  preg_replace("/<!--.+?-->/s", "", $this->_source);
      if (preg_match("/mso-style/", $this->_source)) {
        $xml = new DOMDocument('1.0', 'iso-8859-1');
        $str = "<div>".CMbString::convertHTMLToXMLEntities($this->_source)."</div>";
        @$xml->loadXML(utf8_encode($str));

        $xpath = new DOMXpath($xml);
        $elements = $xpath->query("*/style");

        if ($elements != null) {
          foreach ($elements as $_element) {
            if (preg_match("/(header|footer)/", $_element->nodeValue) == 0) {
              $_element->parentNode->removeChild($_element);
            }
          }
        }
        $this->_source = substr($xml->saveHTML(), 5, -7);

        // La fonction saveHTML ne ferme pas les tags br, hr et img
        $this->_source = str_replace("<br>", "<br/>",  $this->_source);
        $this->_source = str_replace("<hr>", "<hr/>",  $this->_source);
        $this->_source = preg_replace("/<hr class=\"pagebreak\">/", "<hr class=\"pagebreak\"/>", $this->_source);
        $this->_source = preg_replace("/<img([^>]+)>/", "<img$1/>", $this->_source);
      }
    }
  }

  /**
   * Charge les composants d'un modèle
   * 
   * @return void 
   */
  function loadComponents() {
    $this->_ref_header = $this->loadFwdRef("header_id" , true);
    $this->_ref_footer = $this->loadFwdRef("footer_id" , true);
    $this->loadIntroConclusion();
  }

  /**
   * Charge l'introduction et la conclusion
   * 
   * @return void 
   */
  function loadIntroConclusion() {
    $this->_ref_preface = $this->loadFwdRef("preface_id", true);
    $this->_ref_ending  = $this->loadFwdRef("ending_id" , true);
  }

  /**
   * Charge le modèle de référence du document
   * 
   * @return CCompteRendu
   */
  function loadModele() {
    return $this->_ref_modele = $this->loadFwdRef("modele_id");
  }

  /**
   * Charge le fichier unique d'un document / modèle
   * 
   * @return CFile
   */
  function loadFile() {
    return $this->_ref_file = $this->loadUniqueBackRef("files");
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $object = $this->_ref_object;
    $object->loadRefsFwd();

    // Utilisateur
    $this->_ref_user = new CMediusers();
    if ($this->user_id) {
      $this->_ref_user->load($this->user_id);
    }
    elseif ($this->object_id) {
      switch ($this->object_class) {
        case "CConsultation" :
          /** @var $object CConsultation */
          $this->_ref_user->load($object->_ref_plageconsult->chir_id);
          break;
        case "CConsultAnesth" :
          /** @var $object CConsultAnesth */
          $object->_ref_consultation->loadRefsFwd();
          $this->_ref_user->load($object->_ref_consultation->_ref_plageconsult->chir_id);
          break;
        case "COperation" :
          /** @var $object COperation */
          $this->_ref_user->load($object->chir_id);
          break;
      }
    }

    // Fonction
    $this->_ref_function = new CFunctions;
    if ($this->function_id) {
      $this->_ref_function->load($this->function_id);
    }

    // Etablissement
    $this->_ref_group = new CGroups();
    if ($this->group_id) {
      $this->_ref_group->load($this->group_id);
    }
  }

  /**
   * Charge les modèles par catégorie
   * 
   * @param string  $catName nom de la catégorie
   * @param array   $where1  [optional]
   * @param string  $order   [optional]
   * @param boolean $horsCat [optional]
   * 
   * @return array
   */
  static function loadModeleByCat($catName, $where1 = null, $order = "nom", $horsCat = null){
    $ds = CSQLDataSource::get("std");
    $where = array();
    if (is_array($catName)) {
      $where = array_merge($where, $catName);
    }
    elseif (is_string($catName)) {
      $where["nom"] = $ds->prepare("= %", $catName);
    }
    $category = new CFilesCategory;
    $resultCategory = $category->loadList($where);
    $documents = array();

    if (count($resultCategory) || $horsCat) {
      $where = array();
      if ($horsCat) {
        $resultCategory[0] = "";
        $where[] = "file_category_id IS NULL OR file_category_id ".
          CSQLDataSource::prepareIn(array_keys($resultCategory));
      }
      else {
        $where["file_category_id"] = CSQLDataSource::prepareIn(array_keys($resultCategory));
      }
      $where["object_id"] = " IS NULL";
      if ($where1) {
        if (is_array($where1)) {
          $where = array_merge($where, $where1);
        }
        elseif (is_string($where1)) {
          $where[] = $where1;
        }
      }
      $resultDoc = new CCompteRendu;
      $documents = $resultDoc->loadList($where, $order);
    }
    return $documents;
  }

  /**
   * Charge les correspondants d'un document
   * 
   * @return array 
   */
  function loadRefsCorrespondantsCourrier() {
    return $this->_refs_correspondants_courrier = $this->loadBackRefs("correspondants_courrier");
  }

  /**
   * Charge les correspondants d'un document triés par tag puis par cible
   * 
   * @return array 
   */
  function loadRefsCorrespondantsCourrierByTagGuid() {
    if (!$this->_refs_correspondants_courrier) {
      $this->loadRefsCorrespondantsCourrier();
    }
    foreach ($this->_refs_correspondants_courrier as $_corres) {
      $guid = "$_corres->object_class-$_corres->object_id";
      $this->_refs_correspondants_courrier_by_tag_guid[$_corres->tag][$guid] = $_corres;
    }
  }

  /**
   * Fusion de correspondants
   * 
   * @param array &$destinataires tableau de destinataires
   * 
   * @return void
   */
  function mergeCorrespondantsCourrier(&$destinataires) {
    $this->loadRefsCorrespondantsCourrierByTagGuid();

    if (!isset($this->_refs_correspondants_courrier_by_tag_guid["correspondant"])) {
      return;
    }

    /** @var CCorrespondantCourrier[] $correspondants */
    $correspondants = $this->_refs_correspondants_courrier_by_tag_guid["correspondant"];

    if (!isset($destinataires["CMedecin"])) {
      $destinataires["CMedecin"] = array();
    }

    $keys_corres = array_keys($destinataires["CMedecin"]);

    foreach ($correspondants as $key => $_correspondant) {
      if (!array_key_exists($key, $keys_corres)) {
        /** @var CMedecin $_medecin */
        $_medecin = $_correspondant->loadTargetObject();
        $dest = new CDestinataire("correspondant");
        $dest->nom = $_medecin->_view;
        $dest->adresse = $_medecin->adresse;
        $dest->cpville = "$_medecin->cp $_medecin->ville";
        $dest->email   = $_medecin->email;
        $dest->_guid_object = $_medecin->_guid;

        $destinataires["CMedecin"][$_medecin->_id] = $dest;
      }
    }
  }

  /**
   * Charge tous les modèles pour une classe d'objets associés à un utilisateur
   *
   * @param integer $id           Identifiant du propriétaire
   * @param string  $owner        Type de propriétaire du modèle: prat, func ou etab
   * @param string  $object_class string  Nom de la classe d'objet, optionnel. Doit être un CMbObject
   * @param string  $type         Type de composant, optionnel
   * @param bool    $fast_edit    Inclue les modèles en édition rapide
   * @param string  $order        Ordre de tri de la liste
   *
   * @return CCompteRendu[][] Par propriétaire: prat => CCompteRendu[], func => CCompteRendu[], etab => CCompteRendu[]
   */
  static function loadAllModelesFor($id, $owner = 'prat', $object_class = null, $type = null, $fast_edit = true, $order = "") {
    $modeles = array(
      "prat" => array(),
      "func" => array(),
      "etab" => array(),
    );

    if (!$id) {
      return $modeles;
    }

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

    if (!$fast_edit) {
      $where["fast_edit"]     = " = '0'";
      $where["fast_edit_pdf"] = " = '0'";
    }

    if (!$order) {
      $order = "object_class, type, nom";
    }

    switch ($owner) {
      case 'prat': // Modèle du praticien
        $prat = new CMediusers();
        if (!$prat->load($id)) {
          return $modeles;
        }
        $prat->loadRefFunction();

        $where["user_id"]     = "= '$prat->_id'";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = "IS NULL";
        $modeles["prat"] = $modele->loadlist($where, $order);

      case 'func': // Modèle de la fonction
        if (isset($prat)) {
          $func_id = $prat->function_id;
        }
        else {
          $func = new CFunctions();
          if (!$func->load($id)) {
            return $modeles;
          }
          $func_id = $func->_id;
        }

        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "= '$func_id'";
        $where["group_id"]    = "IS NULL";
        $modeles["func"] = $modele->loadlist($where, $order);

      case 'etab': // Modèle de l'établissement
        $etab_id = CGroups::loadCurrent()->_id;
        if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) {
            return $modeles;
          }
          $etab_id = $etab->_id;
        }
        elseif (isset($func)) {
          $etab_id = $func->group_id;
        }
        elseif (isset($func_id)) {
          $func = new CFunctions();
          $func->load($func_id);

          $etab_id = $func->group_id;
        }

        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = " = '$etab_id'";
        $modeles["etab"] = $modele->loadlist($where, $order);
        break;

      default: 
        trigger_error("Wrong type '$owner'", E_WARNING);
    }

    return $modeles;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadContent();
    $this->loadFile();
    $this->_date = $this->loadFirstLog()->date;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!($this->_ref_user || $this->_ref_function || $this->_ref_group) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }

    $this->loadRefAuthor();

    if ($this->_ref_author->_id == CMediusers::get()->_id) {
      $can = new CCanDo();
      $can->read = $can->edit = 1;
      return $can;
    }

    if ($this->_ref_object->_id) {
      $can = $this->_ref_object->getPerm($permType);
    }
    elseif ($this->_ref_user->_id) {
      $can = $this->_ref_user->getPerm($permType);
    }
    elseif ($this->_ref_function->_id) {
      $can = $this->_ref_function->getPerm($permType);
    }
    else {
      $can = $this->_ref_group->getPerm($permType);
    }
    return $can;
  }

  /**
   * Vérifie si l'enregistrement du modèle est possible.
   * 
   * @return string 
   */
  function check() {
    $this->completeField("type", "header_id", "footer_id", "object_class");
    // Si c'est un entête ou pied, et utilisé dans des documents dont le type ne correspond pas au nouveau
    // alors pas d'enregistrement
    if (in_array($this->type, array("footer", "header"))) {
      $doc = new CCompteRendu;
      $where = 'object_class != "'. $this->object_class.
          '" and ( header_id ="' . $this->_id .
          '" or footer_id ="' . $this->_id . '")';;
      if ($doc->countList($where)) {
        return "Des documents sont rattachés à ce pied de page (ou entête) et ils ont un type différent";
      }
    }
    // Si c'est un document dont le type de l'en-tête, de l'introduction, de la conclusion
    // ou du pied de page ne correspond pas à son nouveau type, alors pas d'enregistrement
    if ($this->header_id) {
      $header = new CCompteRendu;
      $header->load($this->header_id);
      if ($header->object_class != $this->object_class) {
        return "Le document n'est pas du même type que son entête";
      }
    }

    if ($this->footer_id) {
      $header = new CCompteRendu;
      $header->load($this->footer_id);
      if ($header->object_class != $this->object_class) {
        return "Le document n'est pas du même type que son pied de page";
      }
    }

    if ($this->preface_id) {
      $preface = new CCompteRendu;
      $preface->load($this->preface_id);
      if ($preface->object_class != $this->object_class) {
        return "Le document n'est pas du même type que son introduction";
      }
    }

    if ($this->ending_id) {
      $ending = new CCompteRendu;
      $ending->load($this->ending_id);
      if ($ending->object_class != $this->object_class) {
        return "Le document n'est pas du même type que sa conclusion";
      }
    }

    return parent::check();
  }

  /**
   * Enregistrement du document / modèle
   * 
   * @return string 
   */
  function store() {
    $this->completeField("content_id", "_source");

    // Prevent source modified wben sending, comparison is working when editing
    $this->loadContent($this->_send);

    $source_modified = 
      $this->_ref_content->content != $this->_source || 
      $this->fieldModified("margin_top") || 
      $this->fieldModified("margin_left") || 
      $this->fieldModified("margin_right") || 
      $this->fieldModified("margin_bottom") || 
      $this->fieldModified("page_height") || 
      $this->fieldModified("page_width") || 
      $this->fieldModified("header_id") ||
      $this->fieldModified("preface_id") ||
      $this->fieldModified("ending_id") || 
      $this->fieldModified("footer_id");

    if ($source_modified) {
      // Bug IE : delete id attribute
      $this->_source = CCompteRendu::restoreId($this->_source);

      // Empty PDF File
      /** @var CFile $_file */
      foreach ($this->loadBackRefs("files") as $_file) {
        $_file->file_empty();
      }

      // Send status to obsolete
      $this->completeField("etat_envoi");
      if ($source_modified && $this->etat_envoi == "oui") {
        $this->etat_envoi = "obsolete";
      }
    }

    $this->_ref_content->content = $this->_source;

    if ($msg = $this->_ref_content->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }

    // Prevent modele_id = compte_rendu_id
    // But, allow to save the content
    if ($this->_id === $this->modele_id) {
      $this->modele_id = "";
    }

    // Detect the fields not completed
    $matches = array();
    preg_match_all("/(field|name)\">(\[)+[^\]]+(\])+<\/span>/ms", $this->_source, $matches);
    $this->fields_missing = count($matches[0]);

    if (!$this->content_id ) {
      $this->content_id = $this->_ref_content->_id;
    }

    if (!$this->_id) {
      $this->author_id = CMediusers::get()->_id;
    }

    $this->version++;

    return parent::store();
  }

  /**
   * Suppression de document / modèle
   * 
   * @return string
   */
  function delete() {
    $this->completeField("content_id");
    $this->loadContent(false);
    $this->loadRefsFiles();

    // Remove PDF preview

    foreach ($this->_ref_files as $_file) {
      $_file->delete();
    }

    if ($msg = parent::delete()) { 
      return $msg; 
    }

    // Remove content
    return $this->_ref_content->delete();
  }

  /**
   * Envoi de document
   * 
   * @return string|null
   */
  function handleSend() {
    if (!$this->_send) {
      return null;
    }

    $this->loadFile();

    $this->completeField("nom", "_source");

    return parent::handleSend();
  }

  /**
   * Tell whether object has a document with the same name has this one
   * 
   * @param CMbObject $object Object to test with
   * 
   * @return boolean
   */
  function existsFor(CMbObject $object) {
    $ds = $this->_spec->ds;
    $doc = new CCompteRendu();
    $doc->setObject($object);
    $doc->nom = $ds->escape($this->nom);
    return $doc->countMatchingList();
  }

  /**
   * Construit un tableau de traduction des classes pour lesquelles la fonction filltemplate existe
   * 
   * @return array
   */
  static function getTemplatedClasses() {
    if (self::$templated_classes !== null) {
      return self::$templated_classes;
    }

    $all_classes = array(
      "CConsultAnesth", "CConsultation",
      "COperation", "CPatient", "CSejour"
    );
    /*$all_classes = array(
      "CBloodSalvage", "CConsultAnesth", "CConsultation", "CDossierMedical", "CRPU",
      "CFunctions", "CGroups", "CMediusers", "COperation", "CPatient", "CSejour"
    );*/
    if (CModule::getActive("dPprescription")) {
      $all_classes[] = "CPrescription";
    }
    $installed = CApp::getInstalledClasses($all_classes);

    $classes = array();
    foreach ($installed as $class) {
      if (is_method_overridden($class, 'fillTemplate') || is_method_overridden($class, 'fillLimitedTemplate')) {
        $classes[$class] = CAppUI::tr($class);
      }
    }

    return self::$templated_classes = $classes;
  }

  /**
   * Construit une source html
   *
   * @param string $htmlcontent html source to use if not a model
   * @param string $mode        [optional]
   * @param array  $margins     [optional]
   * @param string $font        Font name
   * @param string $size        Font size
   * @param string $type        [optional]
   * @param string $header      [optional]
   * @param int    $sizeheader  [optional]
   * @param string $footer      [optional]
   * @param int    $sizefooter  [optional]
   * @param string $preface     [optional]
   * @param string $ending      [optional]
   *
   * @return string
   */
  function loadHTMLcontent(
      $htmlcontent,
      $mode = "modele",
      $margins = array(),
      $font = "",
      $size = "",
      $type = "body",
      $header = "",
      $sizeheader = 0,
      $footer = "",
      $sizefooter = 0,
      $preface = "",
      $ending = ""
  ) {
    $default_font = $font;
    $default_size = $size;

    if ($default_font == "") {
      $default_font = CAppUI::conf("dPcompteRendu CCompteRendu default_font");
    }

    if ($default_size == "") {
      $default_size = CAppUI::conf("dPcompteRendu CCompteRendu default_size");
    }

    $style = file_get_contents("style/mediboard/htmlarea.css") .
      "@page {
         margin-top:    {$margins[0]}cm;
         margin-right:  {$margins[1]}cm;
         margin-bottom: {$margins[2]}cm;
         margin-left:   {$margins[3]}cm;
       }
       body, table {
         font-family: $default_font;
         font-size: $default_size;
       }
       body {
         margin:  0;
         padding: 0;
       }
       .orig {
         display: none;
       }";

    $content = "";
    $position = array(
      "header" => "top",
      "footer" => "bottom"
    );

    if ($mode == "modele") {
      switch ($type) {
        case "header":
        case "footer":
          $position = $position[$type];
          $sizeheader = $sizeheader != '' ? $sizeheader : 50;

          $style .= "
            #{$type} {
              height: {$sizeheader}px;
              {$position}: 0cm;
              width: auto;
            }";

          $content =  "<div id=\"$type\">$htmlcontent</div>";
          break;
        case "body":
        case "preface":
        case "ending":
          if ($header) {
            $sizeheader = $sizeheader != '' ? $sizeheader : 50;
            $padding_top = $sizeheader;

            $style .= "
                @media print {
                  body { 
                    margin-top: {$padding_top}px;
                  }
                  #header {
                    height: {$sizeheader}px;
                    top: 0cm;
                  }
                }";

            $content .= "<div id=\"header\">$header</div>";
          }
          if ($footer) {
            $sizefooter = $sizefooter != '' ? $sizefooter : 50;
            $padding_bottom = $sizefooter;
            $style .= "
                @media print {
                  body { 
                    margin-bottom: {$padding_bottom}px;
                  }
                  #footer {
                    height: {$sizefooter}px;
                    bottom: 0cm;
                  }
                }";
            $content .= "<div id=\"footer\">$footer</div>";
          }
          if ($preface) {
            $htmlcontent = "$preface<br />" . $htmlcontent;
          }
          if ($ending) {
            $htmlcontent .= "<br />$ending";
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

  /**
   * Generate a pdf preview for the document
   * 
   * @param boolean $force_generating [optional]
   * 
   * @return string|null
   */
  function makePDFpreview($force_generating = false) {
    if ((!CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") ||!CAppUI::pref("pdf_and_thumbs"))
        && !$force_generating
    ) {
      return null;
    }

    $this->loadRefsFwd();
    $file = $this->loadFile();

    // Fichier existe déjà et rempli
    if ($file->_id && filesize($file->_file_path)) {
      return null;
    }

    // Création du CFile si inexistant
    if (!$file->_id) {
      $file->setObject($this);
      $file->file_name  = $this->nom . ".pdf";
      $file->file_type  = "application/pdf";
      $file->author_id   = CMediusers::get()->_id;
      $file->fillFields();
      $file->updateFormFields();
      $file->forceDir();
    } 

    // Génération du contenu PDF 
    $margins = array(
      $this->margin_top, 
      $this->margin_right, 
      $this->margin_bottom, 
      $this->margin_left,
    );

    $this->loadContent();
    $content = $this->loadHTMLcontent($this->_source, '', $margins, CCompteRendu::$fonts[$this->font], $this->size);
    $htmltopdf = new CHtmlToPDF();
    $htmltopdf->generatePDF($content, 0, $this->_page_format, $this->_orientation, $file);
    $file->file_size = filesize($file->_file_path);
    $this->_ref_file = $file;

    return $this->_ref_file->store();
  }

  /**
   * Generate the html source from a modele. Can use an optionnal header, footer
   * and another source.
   * 
   * @param string $other_source [optional]
   * @param int    $header_id    [optional]
   * @param int    $footer_id    [optional]
   * 
   * @return string
   */
  function generateDocFromModel($other_source = null, $header_id = null, $footer_id = null) {
    $source = $this->_source;

    if ($other_source) {
      $source = $other_source;
    }

    $this->loadComponents();

    $header  = $this->_ref_header;
    $footer  = $this->_ref_footer;
    $preface = $this->_ref_preface;
    $ending  = $this->_ref_ending;

    if ($header_id) {
      $header->load($header_id);
    }
    if ($footer_id) {
      $footer->load($footer_id);
    }

    $header->loadContent();
    $footer->loadContent();
    $preface->loadContent();
    $ending->loadContent();

    if ($preface->_id) {
      $source = "$preface->_source<br />".$source;
    }

    if ($ending->_id) {
      $source .= "<br />$ending->_source";
    }

    if ($header->_id || $footer->_id) {
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
        $header->_source = "<div id=\"header\">$header->_source</div>";

        if (!CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") || !CAppUI::pref("pdf_and_thumbs")) {      
          $header->height += 20;
        }
      }

      if ($footer->_id) {
        $footer->loadContent();
        $footer->_source = "<div id=\"footer\">$footer->_source</div>";

        if (!CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") || !CAppUI::pref("pdf_and_thumbs")) {
          $footer->height += 20;
        }
      }

      $style.= "
        @media print { 
          body { 
            margin-top: {$header->height}px;
          }
          hr.pagebreak {
            padding-top: {$header->height}px;
          }
        }";

      $style .="
        @media dompdf {
          body {
            margin-bottom: {$footer->height}px;
          }
          hr.pagebreak {
            padding-top: 0px;
          }
        }</style>";

      $source = "<div id=\"body\">$source</div>";
      $source = $style . $header->_source . $footer->_source . $source;
    }

    return $source;
  }

  /**
   * Patch the disappearance of an html attribute
   * 
   * @param string $source source to control
   * 
   * @return string 
   */
  static function restoreId($source) {
    if (strpos($source, '<div id="body"') === false &&
        strpos($source, "<div id='body'") === false &&
        strpos($source, "@media dompdf")  !== false
    ) {

      $xml = new DOMDocument('1.0', 'iso-8859-1');
      $xml->loadXML("<div>".utf8_encode(CMbString::convertHTMLToXMLEntities($source))."</div>");
      $xpath = new DOMXpath($xml);

      /** @var DOMElement $last_div */
      $last_div = null;

      // Test header id
      $elements = $xpath->query("//div[@id='header']");

      if ($elements->length) {
        $last_div = $elements->item(0);
        $last_div = $last_div->nextSibling;
        while ($last_div && $last_div->nodeType != 1) {
          $last_div = $last_div->nextSibling;
        }
        if ($last_div->getAttribute("id") == "footer") {
          $last_div = $last_div->nextSibling;
        }
      }

      // Or footer id
      if (!$last_div) {
        $last_div = $xpath->query("//div[@id='footer']")->item(0);
        $last_div = $last_div->nextSibling;
        while ($last_div && $last_div->nodeType != 1) {
          $last_div = $last_div->nextSibling;
        }
      }

      $div_body = $xml->createElement("div");
      $id_body = $xml->createAttribute("id");
      $id_value = $xml->createTextNode("body");
      $id_body->appendChild($id_value);
      $div_body->appendChild($id_body);

      $div_body = $last_div->parentNode->insertBefore($div_body, $last_div);

      while ($elt_to_move = $xpath->query("//div[@id='body']")->item(0)->nextSibling) {
        $div_body->appendChild($elt_to_move->parentNode->removeChild($elt_to_move));
      }

      // Substring to remove the header of the xml output, and div surrounded
      $source = substr($xml->saveXML(), 27, -7); 
    }
    return $source;
  }

  /**
   * User stats on models
   * 
   * @return array
   * @see parent::getUsersStats();
   */
  function getUsersStats() {
    $ds = $this->_spec->ds;
    $query = "
      SELECT 
        COUNT(`compte_rendu_id`) AS `docs_count`, 
        SUM(LENGTH(`content_html`.`content`)) AS `docs_weight`,
        `author_id` AS `owner_id`
      FROM `compte_rendu` 
      LEFT JOIN `content_html` ON `compte_rendu`.`content_id` = `content_html`.`content_id`
      GROUP BY `owner_id`
      ORDER BY `docs_weight` DESC";
    return $ds->loadList($query);
  }

  /**
   * Advanced user stats on modeles
   * 
   * @param string $user_ids identifiants of users
   * 
   * @return array
   * @see parent::getUsersStatsDetails();
   */
  function getUsersStatsDetails($user_ids) {
    $ds = $this->_spec->ds;
    $in_owner = $ds->prepareIn($user_ids);
    $query = "
      SELECT 
        COUNT(`compte_rendu_id`) AS `docs_count`, 
        SUM(LENGTH(`content_html`.`content`)) AS `docs_weight`,
        `object_class`, 
        `file_category_id` AS `category_id`
      FROM `compte_rendu` 
      LEFT JOIN `content_html` ON `compte_rendu`.`content_id` = `content_html`.`content_id`
      WHERE `author_id` $in_owner
      GROUP BY `object_class`, `category_id`";
    return $ds->loadList($query);
  }

  /**
   * Return the content of the document in plain text
   *
   * @param string $encoding The encoding, default UTF-8
   *
   * @return string
   */
  function getPlainText($encoding = "UTF-8") {
    if (!$this->_source) {
      $this->loadContent(true);
    }

    return CMbString::htmlToText($this->_source, $encoding);
  }

  /**
   * Retourne la source d'un document générée depuis le modèle
   *
   * @return string
   */
  function getFullContentFromModel() {
    $this->loadContent();
    $margins = array(
      $this->margin_top, 
      $this->margin_right, 
      $this->margin_bottom, 
      $this->margin_left);
    $content = $this->generateDocFromModel();
    return $this->loadHTMLcontent($content, '', $margins, CCompteRendu::$fonts[$this->font], $this->size);
  }

  /**
   * Stream document for object
   *
   * @param CCompteRendu $compte_rendu Document
   * @param CMbObject    $object       Object
   * @param string       $factory      Factory name
   *
   * @return void
   */
  static function streamDocForObject($compte_rendu, $object, $factory = "") {
    ob_clean();
    $template = new CTemplateManager();
    $source = $compte_rendu->getFullContentFromModel();
    $object->fillTemplate($template);
    $template->renderDocument($source);
    $htmltopdf = new CHtmlToPDF($factory);
    $htmltopdf->generatePDF($template->document, 1, $compte_rendu->_page_format, $compte_rendu->_orientation, new CFile());
    CApp::rip();
  }

  /**
   * Retourne un modèle de nom prédéfini pour un utilisateur et une classe donnés
   *
   * @param CMediusers $user         User
   * @param string     $object_class Target Class
   * @param string     $name         Model Name
   *
   * @return CCompteRendu|null
   */
  static function getSpecialModel($user, $object_class, $name) {
    if (!isset(self::$special_names[$object_class][$name])) {
      self::error("no_special", $object_class, $name); 
      return null;
    }

    $model = new CCompteRendu();

    if (!$user->_id) {
      return $model;
    }

    $model->nom = $name;
    $model->object_class = $object_class;
    $model->type = CCompteRendu::$special_names[$object_class][$name];

    // Utilisateur
    $model->user_id = $user->_id;
    $model->loadMatchingObject();
    if ($model->_id) {
      return $model;
    }


    // Fonction
    $model->user_id = null;
    $model->function_id = $user->function_id;
    $model->loadMatchingObject();
    if ($model->_id) {
      return $model;
    }

    // Etablissement
    $user->loadRefFunction();
    $model->function_id = null;
    $model->group_id = $user->_ref_function->group_id;
    $model->loadMatchingObject();

    return $model;
  }

  /**
   * Retourne le dernier log de modification d'un document
   *
   * @return CUserLog
   */
  function loadLastLogForContent() {
    $log = new CUserLog();
    $log->object_class = "CContentHTML";
    $log->object_id = $this->content_id;
    $log->loadMatchingObject("date DESC");
    return $log;
  }

  /**
   * Retourne le dernier utilisateur qui a modifié le document
   *
   * @return CUser
   */
  function loadLastWriter() {
    $user = $this->loadLastLogForField("version")->_ref_user;
    if (!$user) {
      $this->loadRefsFwd();
      $user = $this->_ref_user;
    }

    $user->loadFirstLog();

    return $user;
  }

  /**
   * Remplace l'entête ou le pied de page dans une source html
   *
   * @param string $source       HTML source
   * @param int    $component_id Id of the component
   * @param string $type         Type of the component
   *
   * @return string
   */
  static function replaceComponent($source, $component_id, $type="header") {
    if (strpos($source, "<style type=\"text/css\">") === false) {
      $source = "<style type=\"text/css\">
        #header {
          height: 0px;
          /*DOMPDF top: 0;*/
        }

        #footer {
          height: 0px;
          /*DOMPDF bottom: 0;*/
        }
        @media print {
          body {
            margin-top: 0px;
          }
          hr.pagebreak {
            padding-top: 0px;
          }
        }
        @media dompdf {
          body {
            margin-bottom: 0px;
          }
          hr.pagebreak {
            padding-top: 0px;
          }
        }</style>
        <div id=\"body\">".
          $source.
        "</div>";
    };

    switch ($type) {
      case "header":
        $header = new CCompteRendu();
        $header->load($component_id);
        $header->loadContent(true);


        if ($header->_source) {
          $header->_source = "<div id=\"header\">".$header->_source."</div>";
        }

        $height = $header->height ? $header->height : 0;
        $source = preg_replace("/(#header\s*\{\s*height:\s*)([0-9]*[\.0-9]*)px;/", '${1}'.$height.'px;',   $source);
        $source = preg_replace("/(body\s*\{\s*margin-top:\s*)([0-9]*[\.0-9]*)px;/", '${1}'.$height.'px;',  $source);
        $source = preg_replace("/(body\s*\{\s*padding-top:\s*)([0-9]*[\.0-9]*)px;/", '${1}'.$height.'px;', $source);
        $source = preg_replace("/(hr.pagebreak\s*\{\s*padding-top:\s*)([0-9]*[\.0-9]*)px;/", '${1}'.$height.'px;', $source, 1);

        $pos_style  = strpos($source, "</style>") + 9;
        $pos_header = strpos($source, "<div id=\"header\"");
        $pos_footer = strpos($source, "<div id=\"footer\"");
        $pos_body   = strpos($source, "<div id=\"body\">");

        if ($pos_header) {
          if ($pos_footer) {
            $source = substr_replace($source, $header->_source, $pos_header, $pos_footer - $pos_header);
          }
          else {
            $source = substr_replace($source, $header->_source, $pos_header, $pos_body - $pos_header);
          }
        }
        else {
          if ($pos_footer) {
            $source = substr_replace($source, $header->_source, $pos_style, $pos_footer - $pos_style);
          }
          else {
            $source = substr_replace($source, $header->_source, $pos_style, 0);
          }
        }
        break;
      case "footer":
        $footer = new CCompteRendu();
        $footer->load($component_id);
        $footer->loadContent(true);

        if ($footer->_source) {
            $footer->_source = "<div id=\"footer\">".$footer->_source."</div>";
        }
        $height = $footer->height ? $footer->height : 0;
        $source = preg_replace("/(#footer\s*\{\s*footer:\s*)([0-9]+[\.0-9]*)px;/", '${1}'.$height.'px;',     $source);
        $source = preg_replace("/(body\s*\{\s*margin-bottom:\s*)([0-9]+[\.0-9]*)px;/", '${1}'.$height.'px;', $source);

        $pos_footer = strpos($source, "<div id=\"footer\"");
        $pos_body   = strpos($source, "<div id=\"body\">");
        if ($pos_footer) {
          $source = substr_replace($source, $footer->_source, $pos_footer, $pos_body - $pos_footer);
        }
        else {
          $source = substr_replace($source, $footer->_source, $pos_body, 0);
        }
    }

    return $source;
  }
}
