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
class CCompteRendu extends CDocumentItem implements IIndexableObject {
  // DB Table key
  public $compte_rendu_id;

  // DB References
  public $user_id; // not null when is a template associated to a user
  public $function_id; // not null when is a template associated to a function
  public $group_id; // not null when is a template associated to a group
  public $content_id;
  public $locker_id;
  public $header_id;
  public $footer_id;
  public $preface_id;
  public $ending_id;
  public $modele_id;

  // DB fields
  public $nom;
  public $type;
  public $factory;
  public $language;
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
  public $creation_date;

  // Form fields
  public $_is_document    = false;
  public $_is_modele      = false;
  public $_is_auto_locked = false;
  public $_is_locked      = false;
  public $_owner;
  public $_page_format;
  public $_orientation;
  public $_list_classes;
  public $_count_utilisation;

  // Distant field
  public $_source;

  /** @var CMediusers */
  public $_ref_user;

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

  /** @var CMediusers */
  public $_ref_locker;

  /** @var CCorrespondantCourrier[] */
  public $_refs_correspondants_courrier;
  public $_refs_correspondants_courrier_by_tag_guid;

  // Other fields
  public $_entire_doc;
  public $_ids_corres;
  public $_page_ordonnance;

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

  // Liste des chapitres concernés par l'impression des bons
  static $_chap_bons = array("anapath", "biologie", "imagerie", "consult", "kine");

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
    "CFactureCabinet" => array(
      "[ENTETE FACTURE CABINET]"      => "header",
      "[PIED DE PAGE FACT CABINET]"   => "footer"
    ),
    "CFactureEtablissement" => array(
      "[ENTETE FACTURE ETAB]"         => "header",
      "[PIED DE PAGE FACT ETAB]"      => "footer"
    ),
    "CPatient" => array(
     "[ENTETE MOZAIC]"                => "header",
     "[PIED DE PAGE MOZAIC]"          => "footer"
    ),
    'CDevisCodage' => array(
      '[DEVIS]' => 'body'
    )
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
    $backProps["listes_choix"]            = "CListeChoix compte_rendu_id";
    $backProps["modeles_headed"]          = "CCompteRendu header_id";
    $backProps["modeles_footed"]          = "CCompteRendu footer_id";
    $backProps["modeles_prefaced"]        = "CCompteRendu preface_id";
    $backProps["modeles_ended"]           = "CCompteRendu ending_id";
    $backProps["documents_generated"]     = "CCompteRendu modele_id";
    $backProps["pack_links"]              = "CModeleToPack modele_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier compte_rendu_id";
    $backProps["echanges_hl7v3"]          = "CExchangeHL7v3 object_id cascade";
    $backProps["xds_submission_lot"]      = "CXDSSubmissionLotToDocument object_id";

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
    $props["factory"]          = "enum list|CDomPDFConverter|CWkHtmlToPDFConverter|CPrinceXMLConverter|none";
    $props["language"]         = "enum list|en-EN|es-ES|fr-CH|fr-FR default|fr-FR show|0";
    $props["_list_classes"]    = "enum list|".implode("|", array_keys(CCompteRendu::getTemplatedClasses()));
    $props["_is_locked"]       = "bool default|0";
    $props["locker_id"]        = "ref class|CMediusers purgeable";
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
    $props["creation_date"]    = "dateTime";
    $props["_file_size"]       = "str show|0";

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
    $this->_view        = $this->object_id ? "" : "Modèle : ";
    $this->_view .= $this->nom;

    if ($this->object_id) {
      $modele = $this->loadModele();

      if ($modele->_id && $modele->purgeable) {
        $this->_view = "[temp] " . $this->_view;
      }
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

    foreach (CCompteRendu::$_page_formats as $_key => $_format) {
      if (($_format[0] == $this->page_width && $_format[1] == $this->page_height) ||
        ($_format[1] == $this->page_width && $_format[0] == $this->page_height)
      ) {
        $this->_page_format = $_key;
        break;
      }
    }

    // Formatage de la page  
    if (!$this->_page_format) {
      $page_width         = round((72 / 2.54) * $this->page_width, 2);
      $page_height        = round((72 / 2.54) * $this->page_height, 2);
      $this->_page_format = array(0, 0, $page_width, $page_height);
    }

    $this->_orientation = "portrait";

    if ($this->page_width > $this->page_height) {
      $this->_orientation = "landscape";
    }

    // Le champ valide stocke le user_id de la personne qui l'a verrouillé
    if ($this->_id && $this->valide && !$this->locker_id) {
      $log             = $this->loadLastLogForField("valide");
      $this->locker_id = $log->user_id;
    }
  }

  /**
   * Load locker
   *
   * @return CMediusers
   */
  function loadRefLocker() {
    return $this->_ref_locker = $this->loadFwdRef("locker_id", true);
  }

  /**
   * Charge le contenu html
   * 
   * @param boolean $field_source [optional]
   * 
   * @return void
   */
  function loadContent($field_source = true) {
    /** @var  CContentHTML $content */
    $content = $this->loadFwdRef("content_id", true);
    $this->_ref_content = $content;

    $html = $content->content;
    $html = preg_replace("/#body\s*{\s*padding/", "body { margin", $html);
    $html = preg_replace("/#39/", "#039", $html);

    // Supprimer les sauts de pages dans les entêtes et pieds de pages
    if (in_array($this->type, array('header', 'footer'))) {
      $html = str_ireplace('<hr class="pagebreak" />', '', $html);
    }

    $content->content = $html;

    // Passage de la date de dernière modification du content dans la table compte_rendu
    if (!$content->last_modified && $content->_id) {
      $last_log = $content->loadLastLog();
      if (!$last_log->_id) {
        $last_log = $this->loadLastLog();
      }
      $content->last_modified = $last_log->date;
      $content->store();
    }

    if ($field_source) {
      $this->_source = $content->content;

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
    return $this->_ref_modele = $this->loadFwdRef("modele_id", true);
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
   * Charge l'utilisateur associé au modèle
   *
   * @return CMediusers
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", true);
  }

  /**
   * Charge la fonction associée au modèle
   *
   * @return CFunctions
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * Charge l'établissement associé au modèle
   *
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $object = $this->_ref_object;

    // Utilisateur
    if ($this->user_id) {
      $this->loadRefUser();
    }
    elseif ($this->object_id) {
      switch ($this->object_class) {
        case "CConsultation" :
        case "CSejour":
        case "COperation":
        case "CFactureCabinet":
        case "CFactureEtablissement":
          $this->_ref_user = $object->loadRefPraticien();
          break;
        case "CConsultAnesth" :
          $this->_ref_user = $object->loadRefConsultation()->loadRefPraticien();
      }
    }
    else {
      $this->_ref_user = new CMediusers();
    }

    // Fonction
    $this->loadRefFunction();

    // Etablissement
    $this->loadRefGroup();
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
      $resultDoc = new CCompteRendu();
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
   * @param string  $object_class Nom de la classe d'objet, optionnel. Doit être un CMbObject
   * @param string  $type         Type de composant, optionnel
   * @param bool    $fast_edit    Inclue les modèles en édition rapide
   * @param string  $order        Ordre de tri de la liste
   *
   * @return CCompteRendu[][] Par propriétaire: prat => CCompteRendu[], func => CCompteRendu[], etab => CCompteRendu[]
   */
  static function loadAllModelesFor($id, $owner = 'prat', $object_class = null, $type = null, $fast_edit = true, $order = "") {
    // Accès aux modèles de la fonction et de l'établissement
    $module = CModule::getActive("dPcompteRendu");
    $is_admin = $module && $module->canAdmin();
    $access_function = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_function");
    $access_group    = $is_admin || CAppUI::conf("compteRendu CCompteRendu access_group");
    $modeles = array();
    $modeles["prat"] = array();
    if ($access_function) {
      $modeles["func"] = array();
    }
    if ($access_group) {
      $modeles["etab"] = array();
    }

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
        $modeles["prat"]      = $modele->loadListWithPerms(PERM_READ, $where, $order);

        $sec_func = $prat->loadRefsSecondaryFunctions();
        foreach ($sec_func as $_func) {
          $where["user_id"]              = "IS NULL";
          $where["function_id"]          = "= '$_func->_id'";
          $where["group_id"]             = "IS NULL";
          $modeles["func" . $_func->_id] = $modele->loadListWithPerms(PERM_READ, $where, $order);
        }

      case 'func': // Modèle de la fonction
        if (isset($modeles["func"])) {
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
          $modeles["func"] = $modele->loadListWithPerms(PERM_READ, $where, $order);
        }

      case 'etab': // Modèle de l'établissement
        if (isset($modeles["etab"])) {
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
          $modeles["etab"] = $modele->loadListWithPerms(PERM_READ, $where, $order);
        }
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
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!($this->_ref_user || $this->_ref_function || $this->_ref_group) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }

    $parentPerm = parent::getPerm($permType);

    if (!$this->_id) {
      return $parentPerm;
    }

    if ($this->_id && ($this->author_id == CMediusers::get()->_id)) {
      return $parentPerm;
    }

    if ($this->_ref_object->_id) {
      $parentPerm = $parentPerm && $this->_ref_object->getPerm($permType);
    }
    else {
      if ($this->_ref_user->_id) {
        $parentPerm = $parentPerm && $this->_ref_user->getPerm($permType);
      }
      if ($this->_ref_function->_id) {
        $parentPerm = $parentPerm && $this->_ref_function->getPerm($permType);
      }
      if ($this->_ref_group->_id) {
        $parentPerm = $parentPerm && $this->_ref_group->getPerm($permType);
      }
    }
    return $parentPerm;
  }

  /**
   * Vérification du droit de créer un document au sein d'un contexte donné
   *
   * @param CMbObject $object Contexte de création du Document
   *
   * @return bool Droit de création d'un document
   */
  static function canCreate(CMbObject $object) {
    $cr = new CCompteRendu();
    return $object->canRead() && $cr->canClass()->edit;
  }

  /**
   * Vérification du droit de duplication d'un modèle
   *
   * @return bool
   */
  function canDuplicate() {
    $this->loadTargetObject();
    return self::canCreate($this->_ref_object);
  }

  /**
   * Vérification du droit de verouillage du document
   *
   * @return bool Droit de verrouillage
   */
  function canLock() {
    if (!$this->_id) {
      return false;
    }
    return $this->canEdit();
  }

  /**
   * Vérification du droit de déverouillage du document
   *
   * @return bool Droit de déverrouillage
   */
  function canUnLock() {
    if (!$this->_id) {
      return false;
    }
    if ($this->isAutoLock()) {
      return false;
    }
    if (CMediusers::get()->isAdmin()) {
      return true;
    }
    if (CMediusers::get()->_id == $this->locker_id) {
      return true;
    }
    return false;
  }

  /**
   * Vérification de l'état de verrouillage automatique
   *
   * @return bool Etat de verrouillage automatique du document
   */
  function isAutoLock() {
    $this->_is_auto_locked = false;
    switch ($this->object_class) {
      case "CConsultation" :
        $fix_edit_doc = CAppUI::conf("dPcabinet CConsultation fix_doc_edit");
        if ($fix_edit_doc) {
          $consult = $this->loadTargetObject();
          $consult->loadRefPlageConsult();
          $this->_is_auto_locked = CMbDT::dateTime("+ 24 HOUR", "{$consult->_date} {$consult->heure}") > CMbDT::dateTime();
        }
        break;
      case "CConsultAnesth" :
        $fix_edit_doc = CAppUI::conf("dPcabinet CConsultation fix_doc_edit");
        if ($fix_edit_doc) {
          $consult = $this->loadTargetObject()->loadRefConsultation();
          $consult->loadRefPlageConsult();
          $this->_is_auto_locked = CMbDT::dateTime("+ 24 HOUR", "{$consult->_date} {$consult->heure}") > CMbDT::dateTime();
        }
        break;
      default :
        $this->_is_auto_locked = false;
    }
    if (!$this->_is_auto_locked) {
      $this->loadContent();
      $days = CAppUI::conf("dPcompteRendu CCompteRendu days_to_lock");
      $days = isset($days[$this->object_class]) ?
        $days[$this->object_class] : $days["base"];
      $this->_is_auto_locked = CMbDT::daysRelative($this->_ref_content->last_modified, CMbDT::dateTime()) > $days;
    }
    return $this->_is_auto_locked;
  }

  /**
   * Vérification de l'état de verrouillage du document
   *
   * @return bool Etat de verrouillage du document
   */
  function isLocked() {
    if (!$this->_id) {
      return false;
    }
    return $this->_is_locked = $this->isAutoLock() || $this->valide;
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
      $doc = new CCompteRendu();
      $where = 'object_class != "'. $this->object_class.
          '" and (header_id ="' . $this->_id .
          '" or footer_id ="' . $this->_id . '")' .
          '  and object_id IS NULL';
      if ($doc->countList($where)) {
        return "Des documents sont rattachés à ce pied de page (ou entête) et ils ont un type différent";
      }
    }
    // Si c'est un document dont le type de l'en-tête, de l'introduction, de la conclusion
    // ou du pied de page ne correspond pas à son nouveau type, alors pas d'enregistrement
    if (!$this->object_id && $this->type == "body") {
      $this->loadComponents();
      if ($this->header_id) {
        if ($this->_ref_header->object_class != $this->object_class) {
          return "Le document n'est pas du même type que son entête";
        }
      }

      if ($this->footer_id) {
        if ($this->_ref_footer->object_class != $this->object_class) {
          return "Le document n'est pas du même type que son pied de page";
        }
      }

      if ($this->preface_id) {
        if ($this->_ref_preface->object_class != $this->object_class) {
          return "Le document n'est pas du même type que son introduction";
        }
      }

      if ($this->ending_id) {
        if ($this->_ref_ending->object_class != $this->object_class) {
          return "Le document n'est pas du même type que sa conclusion";
        }
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
    $this->completeField("content_id", "_source", "language", "version");

    if (!$this->creation_date) {
      $this->creation_date = CMbDT::dateTime();
      if ($this->_id) {
        $this->creation_date = $this->loadFirstLog()->date;
      }
    }

    // Prevent source modification when not editing the doc
    $this->loadContent($this->_send || $this->_source === null);

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

    if ($source_modified || $this->fieldModified("valide")) {
      // Empty PDF File
      /** @var CFile $_file */
      foreach ($this->loadBackRefs("files") as $_file) {
        $_file->fileEmpty();
      }
    }

    if ($source_modified) {
      // Bug IE : delete id attribute
      $this->_source = CCompteRendu::restoreId($this->_source);
      $this->doc_size = strlen($this->_source);

      // Send status to obsolete
      $this->completeField("etat_envoi");
      if ($source_modified && $this->etat_envoi == "oui") {
        $this->etat_envoi = "obsolete";
      }
    }

    $this->_ref_content->content = $this->_source;

    if (!$this->_id) {
      $parent_modele = $this->loadModele();
      $parent_modele->loadContent(false);
      // Si issu d'une duplication depuis un document existant, alors on reprend la version du document d'origine
      // L'incrément de version se fait en fin de store
      if ($parent_modele->object_id) {
        $this->version = $parent_modele->version;

        // Si le document existant est verrouillé, alors on l'archive
        if ($parent_modele->valide) {
          $parent_modele->annule = 1;
          $parent_modele->store();
        }
      }
    }

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

    if ($this->factory == "none" || !$this->factory) {
      $this->factory = CAppUI::pref("dPcompteRendu choice_factory");
      if (!$this->factory) {
        $this->factory = "CWkHtmlToPDFConverter";
      }
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
      "COperation", "CPatient", "CSejour", "CFactureCabinet", "CFactureEtablissement", 'CDevisCodage'
    );

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

    if (!count($classes)) {
      $classes["CMbObject"] = CAppUI::tr("CMbObject");
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
   * @param string $auto_print  [optional]
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
      $auto_print = true,
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
    $smarty->assign("auto_print", $auto_print);
    return $smarty->fetch("../../dPcompteRendu/templates/htmlheader.tpl");
  }

  /**
   * Generate a pdf preview for the document
   * 
   * @param boolean $force_generating [optional]
   * @param boolean $auto_print       [optional]
   * 
   * @return string|null
   */
  function makePDFpreview($force_generating = false, $auto_print = true) {
    if ((!CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") ||!CAppUI::pref("pdf_and_thumbs"))
        && !$force_generating
    ) {
      return null;
    }

    $this->loadRefsFwd();
    $file = $this->loadFile();

    // Fichier existe déjà et rempli et que la génération n'est pas forcée
    if (!$force_generating && $file->_id && file_exists($file->_file_path) && filesize($file->_file_path)) {
      return null;
    }

    // Création du CFile si inexistant
    if (!$file->_id || !file_exists($file->_file_path)) {
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
    $content = $this->loadHTMLcontent($this->_source, '', $margins, CCompteRendu::$fonts[$this->font], $this->size, $auto_print);
    $htmltopdf = new CHtmlToPDF($this->factory);
    $htmltopdf->generatePDF($content, 0, $this, $file);
    $file->doc_size = filesize($file->_file_path);
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

    if ($other_source) {
      $source = $other_source;
      // Si on utilise une source existante, l'intro et la conclusion sont déjà inclues
      $preface = new CCompteRendu();
      $ending = new CCompteRendu();
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
        SUM(`doc_size`) AS `docs_weight`,
        `author_id` AS `owner_id`
      FROM `compte_rendu`
      GROUP BY `owner_id`
      ORDER BY `docs_weight` DESC";
    return $ds->loadList($query);
  }

  /**
   * @see parent::getUsersStatsDetails();
   */
  function getUsersStatsDetails($user_ids) {
    $ds = $this->_spec->ds;

    $query = new CRequest();
    $query->addColumn("COUNT(`compte_rendu_id`)", "docs_count");
    $query->addColumn("SUM(`doc_size`)", "docs_weight");
    $query->addColumn("object_class");
    $query->addColumn("file_category_id", "category_id");
    $query->addTable("compte_rendu");
    $query->addGroup("object_class, category_id");

    if (is_array($user_ids)) {
      $in_owner = $ds->prepareIn($user_ids);
      $query->addWhereClause("author_id", $in_owner);
    }

    return $ds->loadList($query->makeSelect());
  }

  /**
   * @see parent::getPeriodicalStatsDetails();
   */
  function getPeriodicalStatsDetails($user_ids, $depth = 8) {
    // Pas de champ date pour le moment
    return array();

    $period_types = array(
      "yearly" => array(
        "format"   => "%Y",
        "unit"     => "YEAR",
      ),
      "monthly" => array(
        "format"   => "%m/%Y",
        "unit"     => "MONTH",
      ),
      "weekly" => array(
        "format"   => "%Y S%U",
        "unit"     => "WEEK",
      ),
      "daily" => array(
        "format"   => "%d/%m",
        "unit"     => "DAY",
      ),
      "hourly" => array(
        "format"   => "%d %Hh",
        "unit"     => "HOUR",
      ),
    );

    $details = array();

    $now = CMbDT::dateTime();
    $doc = new self;
    $ds = $doc->_spec->ds;
    $deeper = $depth + 1;

    foreach ($period_types as $_type => $_period_info) {
      $format = $_period_info["format"];
      $unit   = $_period_info["unit"];

      $request = new CRequest();
      $request->addColumn("DATE_FORMAT(`creation_date`, '$format')", "period");
      $request->addColumn("COUNT(`compte_rendu_id`)", "count");
      $request->addColumn("SUM(`doc_size`)", "weight");
      $date_min = CMbDT::dateTime("- $deeper $unit", $now);
      $request->addWhereClause("creation_date", " > '$date_min'");
      if (count($user_ids)) {
        $request->addWhereClause("author_id", CSQLDataSource::prepareIn($user_ids));
      }
      $request->addGroup("period");
      $results = $ds->loadHashAssoc($request->makeSelect($doc));

      foreach(range($depth, 0) as $i) {
        $period = CMbDT::transform("-$i $unit", $now, $format);
        $details[$_type][$period] = isset($results[$period]) ? $results[$period] : 0;
      }
    }

    return $details;
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
  static function streamDocForObject($compte_rendu, $object, $factory = null) {
    ob_clean();
    $template = new CTemplateManager();
    $source = $compte_rendu->getFullContentFromModel();
    $object->fillTemplate($template);
    $template->renderDocument($source);
    $htmltopdf = new CHtmlToPDF($factory);
    $htmltopdf->generatePDF($template->document, 1, $compte_rendu, new CFile());
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

  /**
   * Loads the related fields for indexing datum
   *
   * @return array
   */
  function getIndexableData () {
    $prat = $this->getIndexablePraticien();
    if (!$prat) {
      $prat = new CMediusers();
    }
    $array["id"]          = $this->_id;
    $array["author_id"]   = $this->author_id;
    $array["prat_id"]     = $prat->_id;
    $array["title"]       = $this->nom;
    $this->loadContent(false);
    $content              = $this->_ref_content;
    $array["body"]        = $this->getIndexableBody($content->content);
    $date = $this->creation_date;
    if (!$date) {
      $date = CMbDT::dateTime();
    }
    $array["date"]        = str_replace("-", "/", $date);
    $array["function_id"] = $prat->function_id;
    $array["group_id"]    = $this->loadRefAuthor()->loadRefFunction()->group_id;
    $array["patient_id"]  = $this->getIndexablePatient()->_id;
    $array["object_ref_id"] = $this->loadTargetObject()->_id;
    $array["object_ref_class"] = $this->loadTargetObject()->_class;

    return $array;
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody($content) {
    return CSearch::getRawText($content);
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return CPatient
   */
  function getIndexablePatient () {
    $object = $this->loadTargetObject();

    if (!$object || !$object->_id) {
      return null;
    }

    if ($object instanceof CPatient) {
      return $object;
    }

    if (in_array("IPatientRelated", class_implements($object))) {
      $object->loadRelPatient();
    }
    else {
      $object->loadRefPatient();
    }

    switch ($this->object_class) {
      case "CConsultAnesth":
        return $object->_ref_consultation->_ref_patient;
        break;

      default:
        return $object->_ref_patient;
    }
  }
  /**
   * Get the praticien_id of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien () {
    $object = $this->loadTargetObject();
    if (!$object || !$object->_id) {
      return null;
    }
    if ($object instanceof CConsultAnesth) {
      $prat = $object->loadRefConsultation()->loadRefPraticien();
    }
    elseif ($object instanceof CPatient) {
      $prat = $this->loadRefAuthor();
    }
    else {
      $prat = $object->loadRefPraticien();
    }
    return $prat;
  }
}

// Ajout des en-têtes de bons pour chacun des chapitres
foreach (CCompteRendu::$_chap_bons as $chapitre) {
  $maj_chap = strtoupper($chapitre);
  CCompteRendu::$special_names["CPrescription"]["[ENTETE BON $maj_chap]"] = "header";
  CCompteRendu::$special_names["CPrescription"]["[PIED DE PAGE BON $maj_chap]"] = "footer";
}