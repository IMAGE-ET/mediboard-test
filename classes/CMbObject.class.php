<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Handles: notes, documents, aides, views, model templates, echanges, idex, affectations personnels (!)
 *
 * @abstract Mediboard business object layer
 */
class CMbObject extends CStoredObject {
  public $_aides         = array(); // Aides � la saisie
  public $_aides_new     = array(); // Nouveau tableau des aides (sans hierarchie)

  /** @var CAideSaisie[][][] */
  public $_aides_all_depends;


  public $_nb_files_docs;
  public $_nb_files;
  public $_nb_cancelled_files;
  public $_nb_docs;
  public $_nb_exchanges;
  public $_nb_exchanges_by_format = array();
  public $_degree_notes;

  /** @var CIdSante400 */
  public $_ref_last_id400;

  /** @var CNote[] */
  public $_ref_notes;

  /** @var CCompteRendu[] */
  public $_ref_documents      = array();

  /** @var CCompteRendu[] */
  public $_ref_documents_by_cat = array();

  /** @var CFile[] */
  public $_ref_files          = array();

  /** @var CFile[][] */
  public $_ref_files_by_cat   = array();

  /** @var CFile[] */
  public $_ref_named_files    = array();

  /** @var CTagItem[] */
  public $_ref_tag_items      = array();

  /** @var CDocumentItem[][] */
  public $_refs_docitems_by_cat = array();

  /** @var CMbObjectConfig */
  public $_ref_object_configs;
  
  public $_ref_affectations_personnel;
  public $_count_affectations_personnel;

  /**
   * Chargement des notes sur l'objet
   *
   * @param int $perm One of PERM_READ | PERM_EDIT
   *
   * @return int Note count
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = array();
    $this->_degree_notes = null;
    $notes_levels = array();
    
    if ($this->_id) {
      $this->_ref_notes = $this->loadBackRefs("notes");
      self::filterByPerm($this->_ref_notes, $perm);

      // Find present levels
      foreach ($this->_ref_notes as $_note) {
        /** @var CNote $_note */
        $notes_levels[$_note->degre] = true;
      }
      
      // Note highest level 
      if (isset($notes_levels["low"])) {
        $this->_degree_notes = "low";
      }

      if (isset($notes_levels["medium"])) {
        $this->_degree_notes = "medium";
      }

      if (isset($notes_levels["high"])) {
        $this->_degree_notes = "high";
      }
    }

    return count($this->_ref_notes);
  }

  /**
   * Load files for object with PERM_READ
   * 
   * @return int|null Files count, null if unavailable
   */
  function loadRefsFiles() {
    if (null == $this->_ref_files = $this->loadBackRefs("files", "file_name")) {
      return null;
    }

    $is_editable = $this->docsEditable();
    // Read permission
    foreach ($this->_ref_files as $_file) {
      /** @var CFile $_file */
      $_file->canDo();
      if (!$_file->canRead()) {
        unset($this->_ref_files[$_file->_id]);
        continue;
      }
      $this->_ref_files_by_name[$_file->file_name] = $_file;
      $_file->_is_editable = $is_editable;
      if ($_file->annule) {
        $this->_nb_cancelled_files++;
      }
    }
    
    return count($this->_ref_files);
  }
  
  /**
   * Load a named file for for the object, supposedly unique
   * 
   * @param string $name Name of the file
   * 
   * @return CFile The named file
   */
  function loadNamedFile($name) {
    return $this->_ref_named_files[$name] = CFile::loadNamed($this, $name);
  }

  /**
   * Load documents for object with PERM_READ
   *
   * @return int|null Files count, null if unavailable
   */
  function loadRefsDocs() {
    if (null == $this->_ref_documents = $this->loadBackRefs("documents", "nom")) {
      return null;
    }

    $is_editable = $this->docsEditable();

    $days = CAppUI::conf("compteRendu CCompteRendu days_to_lock");
    $days = isset($days[$this->_class]) ? $days[$this->_class] : $days["base"];

    global $can;
    $user = CAppUI::$user;
    foreach ($this->_ref_documents as $_doc) {
      /** @var CCompteRendu $_doc */
      $_doc->canDo();

      if (!$can->admin) {
        if (!$_doc->canRead()) {
          unset($this->_ref_documents[$_doc->_id]);
          continue;
        }
        $_doc->_is_editable = $is_editable;

        $last_log = $_doc->loadLastLogForContent();

        // Document verrouill�
        if (($_doc->valide && $_doc->author_id != $user->_id) ||
            (CMbDT::daysRelative($last_log->date, CMbDT::dateTime()) > $days)
        ) {
          $_doc->_is_editable = false;
          $_doc->_can->edit = false;
        }
      }
    }

    return count($this->_ref_documents);
  }

  /**
   * Load documents and files for object and sort by category
   *
   * @return int document + files count
   */
  function loadRefsDocItems() {
    $this->_nb_files = $this->loadRefsFiles();
    $this->_nb_docs  = $this->loadRefsDocs();
    $this->_nb_files_docs = $this->_nb_files + $this->_nb_docs;

    $categories_files = CMbObject::massLoadFwdRef($this->_ref_files, "file_category_id");
    $categories_docs  = CMbObject::massLoadFwdRef($this->_ref_documents, "file_category_id");
    $categories = $categories_docs + $categories_files;

    foreach ($this->_ref_documents as $_document) {
      $cat_name = $_document->file_category_id ? $categories[$_document->file_category_id]->nom : "";
      @$this->_ref_documents_by_cat[$cat_name][] = $_document;
      @$this->_refs_docitems_by_cat[$cat_name][] = $_document;
    }
    foreach ($this->_ref_files as $_file) {
      $cat_name = $_file->file_category_id ? $categories[$_file->file_category_id]->nom : "";
      @$this->_ref_files_by_cat[$cat_name][] = $_file;
      @$this->_refs_docitems_by_cat[$cat_name][] = $_file;
    }

    ksort(@$this->_refs_docitems_by_cat);
  }
  
  /**
   * Count documents
   *
   * @return int
   */
  function countDocs() {
    return $this->_nb_docs = $this->countBackRefs("documents");
  }
  
  /**
   * Count files
   *
   * @return int
   */
  function countFiles(){
    return $this->_nb_files = $this->countBackRefs("files");
  }
  
  /**
   * Count doc items (that is documents and files), delegate when permission type defined
   *
   * @param int $permType Permission type, one of PERM_READ, PERM_EDIT
   *
   * @return int
   */
  function countDocItems($permType = null) {
    $this->_nb_files_docs = $permType ? 
      $this->countDocItemsWithPerm($permType) : 
      $this->countFiles() + $this->countDocs();
    return $this->_nb_files_docs;
  }
  
  /**
   * Count doc items according to given permission
   *
   * @param int $permType Permission type, one of PERM_READ, PERM_EDIT
   *
   * @todo Merge with countDocItems(), unnecessary delegation
   *
   * @return int
   */
  function countDocItemsWithPerm($permType = PERM_READ){
    $this->loadRefsFiles();
    if ($this->_ref_files) {
      self::filterByPerm($this->_ref_files, $permType);
      $this->_nb_files = count($this->_ref_files);
    }
    
    $this->loadRefsDocs();
    if ($this->_ref_documents) {
      self::filterByPerm($this->_ref_documents, $permType);
      $this->_nb_docs = count($this->_ref_documents);
    }
    
    return $this->_nb_files + $this->_nb_docs;
  }
  
  /**
   * Count exchanges, make totals by format
   *
   * @param string $type    Exchange type
   * @param string $subtype Exchange subtype
   *
   * @return int The absolute total
   */
  function countExchanges($type = null, $subtype = null) {
    foreach (CExchangeDataFormat::getAll() as $_data_format) {
      /** @var CExchangeDataFormat $data_format */
      $data_format = new $_data_format;
      if (!$data_format->hasTable()) {
        continue;
      }
      $data_format->object_id    = $this->_id;
      $data_format->object_class = $this->_class;

      $data_format->type      = $type;
      $data_format->sous_type = $subtype;

      $this->_nb_exchanges_by_format[$_data_format] = $data_format->countMatchingList();
    }
    
    foreach ($this->_nb_exchanges_by_format as $_nb_exchange_format) {
      $this->_nb_exchanges += $_nb_exchange_format;
    }
    
    return $this->_nb_exchanges;
  }
      
  
  /**
   * Chargement du dernier identifiant id400
   *
   * @param string $tag Tag � utiliser comme filtre
   *
   * @return CIdSante400
   */
  function loadLastId400($tag = null) {
    $idex = new CIdSante400();
    if ($idex->_ref_module) {
      $idex->loadLatestFor($this, $tag);
      $this->_ref_last_id400 = $idex;
    }
    return $idex;
  } 
    
  /**
   * Load object view information
   *
   * @return void
   */
  function loadView() {
    parent::loadView();
    $this->loadRefsNotes();
  }
  
  /**
   * Load object view information when used in an edit form (see system/ajax_edit_object.php)
   *
   * @return void
   */
  function loadEditView() {
    $this->loadView();
  }
  
  /**
   * Load complete object view information
   *
   * @return void
   */
  function loadComplete() {
    $this->loadRefsNotes();
    $this->loadRefs();
  }
  
  /**
   * Back references global loader
   * DEPRECATED: out of control resouce consumption
   *
   * @return ref Object id
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadExternal();
  }

  /**
   * Load idexs
   *
   * @return void
   */
  function loadExternal() {
    $this->_external = $this->countBackRefs("identifiants");
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["alerts"]                 = "CAlert object_id";
    $backProps["identifiants"]           = "CIdSante400 object_id";
    $backProps["notes"]                  = "CNote object_id";
    $backProps["files"]                  = "CFile object_id";
    $backProps["documents"]              = "CCompteRendu object_id";
    $backProps["permissions"]            = "CPermObject object_id";
    $backProps["logs"]                   = "CUserLog object_id";
    $backProps["affectations_personnel"] = "CAffectationPersonnel object_id";
    $backProps["contextes_constante"]    = "CConstantesMedicales context_id";
    $backProps["modeles_etiquettes"]     = "CModeleEtiquette object_id";
    $backProps["tag_items"]              = "CTagItem object_id";
    $backProps["echange_generique"]      = "CExchangeAny object_id";
    $backProps["observation_result_sets"]= "CObservationResultSet context_id";
    $backProps["configurations"]         = "CConfiguration object_id";
    $backProps["sources_pop"]            = "CSourcePOP object_id";
    $backProps["dicom_exchange"]         = "CExchangeDicom object_id";
      // NE PAS DECOMMENTER CETTE LIGNE, backref impossible pour le moment (cf. Fabien)
    //$backProps["ex_objects"]             = "CExObject object_id";

    return $backProps;
  }
  
  /**
   * Charge toutes les aides � la saisie de l'objet pour un utilisateur donn�
   *
   * @param int    $user_id        Utilisateur
   * @param string $keywords       Permet de filtrer les aides comman�ant par le filtre, si non null
   * @param string $depend_value_1 Valeur de la d�pendance 1 li� � l'aide
   * @param string $depend_value_2 Valeur de la d�pendance 2 li� � l'aide
   * @param string $object_field   Type d'objet concern�
   * @param string $strict         True or False
   *
   * @return void
   */
  function loadAides(
      $user_id,
      $keywords = null,
      $depend_value_1 = null,
      $depend_value_2 = null,
      $object_field = null,
      $strict = "true"
  ) {
    foreach ($this->_specs as $field => $spec) {
      if (isset($spec->helped)) {
        $this->_aides[$field] = array("no_enum" => null);
      }
    }

    // Chargement de l'utilisateur courant
    $user = new CMediusers();
    $user->load($user_id);
    $user->loadRefFunction();
    
    // Pr�paration du chargement des aides
    $ds =& $this->_spec->ds;
    
    // Construction du Where
    $where = array();

    $where[] = "(user_id = '$user_id' OR 
      function_id = '$user->function_id' OR 
      group_id = '{$user->_ref_function->group_id}')";
                
    $where["class"]   = $ds->prepare("= %", $this->_class);

    if ($strict == "true") {
      if ($depend_value_1) {
        $where["depend_value_1"] = " = '$depend_value_1'";
      }
      
      if ($depend_value_2) {
        $where["depend_value_2"] = " = '$depend_value_2'";
      }
    }
    else {
      if ($depend_value_1) {
        $where[] = "(depend_value_1 = '$depend_value_1' OR depend_value_1 IS NULL)";
      }
      if ($depend_value_2) {
        $where[] = "(depend_value_2 = '$depend_value_2' OR depend_value_2 IS NULL)";
      }
    }
    
    if ($object_field) {
      $where["field"] = " = '$object_field'";
    }
    
    // tri par user puis function puis group (ordre invers� pour avoir ce r�sultat)
    $order = "group_id, function_id, user_id, depend_value_1, depend_value_2, name, text";
    
    // Chargement des Aides de l'utilisateur
    $aide = new CAideSaisie();
    // TODO: si on veut ajouter un $limit, il faudrait l'ajouter en argument de la fonction loadAides
    $aides = $aide->seek($keywords, $where, null, null, null, $order);

    $this->orderAides($aides, $depend_value_1, $depend_value_2);
  }

  /**
   * Order aides
   *
   * @param CAideSaisie[] $aides          Aides � la saisie
   * @param string        $depend_value_1 Valeur de la d�pendance 1 li� � l'aide
   * @param string        $depend_value_2 Valeur de la d�pendance 2 li� � l'aide
   *
   * @return void
   */
  function orderAides($aides, $depend_value_1 = null, $depend_value_2 = null) {
    foreach ($aides as $aide) { 
      $owner = CAppUI::tr("CAideSaisie._owner.$aide->_owner");
      $aide->loadRefOwner();
      
      // si on filtre seulement sur depend_value_1, il faut afficher les resultats suivant depend_value_2
      if ($depend_value_1) {
        $depend_field_2 = $aide->_depend_field_2;
        $depend_2 = CAppUI::tr("$this->_class.$aide->_depend_field_2.$aide->depend_value_2");
        if ($aide->depend_value_2) {
          $this->_aides[$aide->field][$owner][$depend_2][$aide->text] = $aide->name;
        }
        else {
          $depend_name_2 = CAppUI::tr("$this->_class-$depend_field_2");
          $this->_aides[$aide->field][$owner]["$depend_name_2 non sp�cifi�"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      // ... et r�ciproquement 
      if ($depend_value_2) {
        $depend_field_1 = $aide->_depend_field_1;
        $depend_1 = CAppUI::tr("$this->_class.$aide->_depend_field_1.$aide->depend_value_1");
        if ($aide->depend_value_1) {
          $this->_aides[$aide->field][$owner][$depend_1][$aide->text] = $aide->name;
        }
        else {
          $depend_name_1 = CAppUI::tr("$this->_class-$depend_field_1");
          $this->_aides[$aide->field][$owner]["$depend_name_1 non sp�cifi�"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      $this->_aides_all_depends[$aide->field][$aide->depend_value_1][$aide->depend_value_2][$aide->_id] = $aide;
      
      // Ajout de l'aide � la liste g�n�rale
      $this->_aides[$aide->field]["no_enum"][$owner][$aide->text] = $aide->name;
    }
    
    $this->_aides_new = $aides;
  }
  
  /**
   * Chargement des affectations de personnel par emplacements
   *
   * @return CAffectationPersonnel[]|null Affections, null if unavailable
   */
  function loadAffectationsPersonnel() {
    // Initialisation
    $personnel = new CPersonnel();
    foreach ($personnel->_specs["emplacement"]->_list as $emplacement) {
      $this->_ref_affectations_personnel[$emplacement] = array();
    }
    
    // Module actif
    if (null == $affectations = $this->loadBackRefs("affectations_personnel")) {
      return null;
    }
    
    $this->_count_affectations_personnel = count($affectations);
    
    // Chargement et classement

    foreach ($affectations as $affectation) {
      /** @var CAffectationPersonnel $affectation */
      $personnel = $affectation->loadRefPersonnel();
      $personnel->loadRefUser()->loadRefFunction();
      $this->_ref_affectations_personnel[$personnel->emplacement][$affectation->_id] = $affectation;
    }
    
    return $this->_ref_affectations_personnel;
  }

  /**
   * Load the object's tag items
   *
   * @param bool $cache Use cache
   *
   * @return array
   */
  function loadRefsTagItems($cache = true) {
    if ($cache && !empty($this->_ref_tag_items)) {
      return $this->_ref_tag_items;
    }
    
    return $this->_ref_tag_items = $this->loadBackRefs("tag_items");
  }

  /**
   * Get the object's tags
   *
   * @param bool $cache Use cache
   *
   * @return array
   */
  function getTags($cache = true) {
    $tag_items = $this->loadRefsTagItems($cache);
    return CMbArray::pluck($tag_items, "_ref_tag");
  }
  
  /**
   * Get the related object by class for template filling
   *
   * @return array Collection of class => id relations
   */
  function getTemplateClasses(){
    return array($this->_class => $this->_id);
  }
  
  /**
   * This function register all templated properties for the object
   * Will load as necessary and fill in values
   *
   * @param CTemplateManager &$template Template manager
   *
   * @return void
   */
  function fillTemplate(&$template) {
  }
   
  /**
   * This function register most important templated properties for the object
   * Won't register distant properties
   * Will load as necessary and fill in values
   *
   * @param CTemplateManager &$template Template manager
   *
   * @return void
   */
  function fillLimitedTemplate(&$template) {
  }
  
  /**
   * This function registers fields for the label printing
   *
   * @param array &$fields Array of fields
   *
   * @return void
   */
  function completeLabelFields(&$fields) {
  }
  
  /**
   * Load object config
   *
   * @return array contains config of class and/or object
   */
  function loadConfigValues() {
    $object_class = $this->_class."Config";
    
    if (!class_exists($object_class)) {
      return;
    }
    
    // Chargement des configs de la classe
    $where = array();
    $where["object_id"]    = " IS NULL";
    /** @var CMbObjectConfig $class_config */
    $class_config = new $object_class;
    $class_config->loadObject($where);

    if (!$class_config->_id) {
      $class_config->valueDefaults();
    }
    
    // Chargement des configs de l'objet
    $object_config = $this->loadUniqueBackRef("object_configs");

    $class_config->extendsWith($object_config);

    $this->_configs = $class_config->getConfigValues();
  }
  
  /**
   * Get value of the object config
   *
   * @return string[]
   */
  function getConfigValues() {
    $configs = array();
    
    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);
    unset($fields["object_id"]);
    foreach ($fields as $_name => $_value) {
      $configs[$_name] = $_value;
    }
    
    return $configs;
  }
  
  
  /**
   * Backward references
   *
   * @return void
   */
  function loadRefObjectConfigs() {
    $object_class = $this->_class."Config";
    if (class_exists($object_class)) {
      $this->_ref_object_configs = $this->loadUniqueBackRef("object_configs");
    }
  }
  
  /**
   * Evaluate if an object is editable according to a date.
   *
   * @return bool
   */
  function docsEditable() {
    // Un admin doit toujours pouvoir modifier un document
    global $can;
    return $can->admin;
  }
    
  /**
   * Returns the path to the class-specific template
   * 
   * @param string $type view|autocomplete|edit
   *
   * @return string|null
   */
  function getTypedTemplate($type) {
    if (!in_array($type, array("view", "autocomplete", "edit"))) {
      return null;
    }
    
    $mod_name = $this->_ref_module->mod_name;
    $template = "$mod_name/templates/{$this->_class}_$type.tpl";
    
    if (!is_file("modules/$template")) {
      $template = "system/templates/CMbObject_$type.tpl";
    }
    
    return "../../$template";
  }
  
  /**
   * Make and return usefull template paths for given object
   *
   * @param string $name One of "view" and "complete"
   *
   * @return string|null Path to wanted template, null if module undefined for object
   */
  function makeTemplatePath($name) {
    if (null == $module = $this->_ref_module) {
      return null;
    }

    $path = "$module->mod_name/templates/$this->_class";
    return "{$path}_{$name}.tpl";
  }
  
  /**
   * Fills the object with random sample data, for testing purposes
   *
   * @param array() $staticsProps Properties to assess
   *
   * @return void
   */
  function sample($staticsProps = array()) {
    foreach ($this->_specs as $key => $spec) {
      if (isset($staticsProps[$key])) {
        $this->$key = $staticsProps[$key];
      }
      elseif ($key[0] != "_") {
        $spec->sample($this, false);
      }
    }
  }

  /**
   * Fills the object with random sample data from database
   *
   * @return void
   */
  function random() {
    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);

    foreach ($fields as $_field => $value) {
      $this->$_field = $this->getRandomValue($_field);
    }
  }

  /**
   * Get random value
   *
   * @param string $field       Field name
   * @param bool   $is_not_null Search field not null
   *
   * @return mixed
   */
  function getRandomValue($field, $is_not_null = false) {
    $ds = $this->getDS();

    $query = new CRequest;
    $query->addSelect($field);
    $query->addTable($this->_spec->table);
    if ($is_not_null) {
      $query->addWhereClause($field, "IS NOT NULL");
    }
    $query->addOrder("RAND()");
    $query->setLimit(1);

    return $ds->loadResult($query->getRequest());
  }

  /**
   * Return idex type if it's special (e.g. IPP/NDA/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
  }
}