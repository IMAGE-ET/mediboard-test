<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * The CDocumentItem class
 */
class CDocumentItem extends CMbMetaObject {
  public $file_category_id;
  
  public $etat_envoi;
  public $author_id;
  public $private;
  public $annule;
  public $doc_size;
  public $type_doc;
  public $type_doc_sisra;

  // Derivated fields
  public $_extensioned;
  public $_no_extension;
  public $_file_size;
  public $_icon_name;

  public $_send_problem;

  // Behavior Field
  public $_send;

  /** @var CMediusers */
  public $_ref_author;

  /** @var CFilesCategory */
  public $_ref_category;

  //DMP
  public $_refs_dmp_document;
  public $_count_dmp_document;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["file_category_id"] = "ref class|CFilesCategory";
    $props["etat_envoi"]       = "enum notNull list|oui|non|obsolete default|non";
    $props["author_id"]        = "ref class|CMediusers";
    $props["private"]          = "bool default|0";
    $props["annule"]           = "bool default|0 show|0";
    $props["doc_size"]         = "num min|0 show|0";
    $type_doc = "";
    if (CModule::getActive("cda")) {
      $jdv_type = CCdaTools::loadJV("CI-SIS_jdv_typeCode.xml");
      foreach ($jdv_type as $_type) {
        $type_doc .= $_type["codeSystem"]."^".$_type["code"]."|";
      }
      $type_doc = substr($type_doc, 0, -1);
    }
    $props["type_doc"]       = (empty($type_doc) ? "str" : "enum list|$type_doc");
    $sisra_types = "";
    if (CModule::getActive("sisra")) {
      $sisra_types = CSisraTools::getSisraTypeDocument();
      $sisra_types = implode("|", $sisra_types);
    }
    $props["type_doc_sisra"] = (empty($sisra_types) ? "str" : "enum list|$sisra_types");
    $props["_extensioned"]   = "str notNull";
    $props["_no_extension"]   = "str notNull";
    $props["_file_size"]    = "str show|1";
    $props["_send_problem"]  = "text";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["dmp_documents"]   = "CDMPDocument object_id";
    $backProps["sisra_documents"] = "CSisraDocument object_id";
    return $backProps;
  }

  /**
   * Return the action on the document for the DMP
   *
   * @return CDMPDocument|null
   */
  function loadDocumentDMP() {
    if (!CModule::getActive("dmp")) {
      return null;
    }
    return $this->_refs_dmp_document = $this->loadBackRefs("dmp_documents", "date DESC");
  }

  /**
   * Count the action on the document for the DMP
   *
   * @return int|null
   */
  function countDocumentDMP() {
    if (!CModule::getActive("dmp")) {
      return null;
    }
    return $this->_count_dmp_document = $this->countBackRefs("dmp_documents");
  }
  
  /**
   * Retrieve content as binary data
   *
   * @return string Binary Content
   */
  function getBinaryContent() {
  }

  /**
   * Retrieve extensioned like file name
   *
   * @return string Binary Content
   */
  function getExtensioned() {
    return $this->_extensioned;
  }

  /**
   * Try and instanciate document sender according to module configuration
   *
   * @return CDocumentSender sender or null on error
   */
  static function getDocumentSender() {
    if (null == $system_sender = CAppUI::conf("dPfiles system_sender")) {
      return null;
    }
    
    if (!is_subclass_of($system_sender, "CDocumentSender")) {
      trigger_error("Instanciation du Document Sender impossible.");
      return null;
    }
    
    return new $system_sender;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_file_size = CMbString::toDecaBinary($this->doc_size);

    $this->getSendProblem();
    $this->loadRefCategory();
  }
  
  /**
   * Retrieve send problem user friendly message
   *
   * @return string Store-like problem message
   */
  function getSendProblem() {
    if ($sender = self::getDocumentSender()) {
      $this->_send_problem = $sender->getSendProblem($this);
    }
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("etat_envoi");
    $this->completeField("object_class");
    $this->completeField("object_id");

    if ($msg = $this->handleSend()) {
      return $msg;
    }

    return parent::store();
  }
  
  /**
   * Handle document sending store behaviour
   *
   * @return string Store-like error message 
   */
  function handleSend() {
    if (!$this->_send) {
      return null;
    }
    
    $this->_send = false;
    
    if (null == $sender = self::getDocumentSender()) {
      return "Document Sender not available";
    }
    
    switch ($this->etat_envoi) {
      case "non" :
        if (!$sender->send($this)) {
          return "Erreur lors de l'envoi.";
        }
        CAppUI::setMsg("Document transmis.");
        break;
      case "oui" :
        if (!$sender->cancel($this)) {
          return "Erreur lors de l'invalidation de l'envoi.";
        }
        CAppUI::setMsg("Document annulé."); 
        break;
      case "obsolete" :
        if (!$sender->resend($this)) {
          return "Erreur lors du renvoi.";
        }
        CAppUI::setMsg("Document annulé/transmis.");
        break;
      default:
        return "Fonction d'envoi '$this->etat_envoi' non reconnue.";
    }
    return null;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefCategory();
    $this->loadRefAuthor();
  }

  /**
   * Load category
   *
   * @return CFilesCategory
   */
  function loadRefCategory() {
    return $this->_ref_category = $this->loadFwdRef("file_category_id", true);
  }

  /**
   * Load author
   *
   * @return CMediusers
   */
  function loadRefAuthor() {
    return $this->_ref_author = $this->loadFwdRef("author_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefAuthor();
    $this->loadRefCategory();

    // Permission de base
    $perm = parent::getPerm($permType);

    // Il faut au moins avoir le droit de lecture sur la catégories
    if ($this->file_category_id) {
      $perm &= $this->_ref_category->getPerm(PERM_READ);
    }

    // Gestion d'un document confidentiel
    if ($this->private) {
      $sameFunction = $this->_ref_author->function_id == CMediusers::get()->function_id;
      $isAdmin = CMediusers::get()->isAdmin();
      $perm &= ($sameFunction || $isAdmin);
    }

    return $perm;
  }
  
  /**
   * Load aggregated doc item ownership
   *
   * @return array collection of arrays with docs_count, docs_weight and author_id keys
   */
  function getUsersStats() {
    return array();
  }
  
  /**
   * Advanced user stats on modeles
   *
   * @param ref[]|null $user_ids User IDs, null if no filter
   *
   * @return array collection of arrays with docs_count, docs_weight, object_class and category_id keys
   */
  function getUsersStatsDetails($user_ids) {
    return array();
  }

  /**
   * Advanced periodical stats on modeles
   *
   * @param ref[]|null $user_ids User IDs, null if no filter
   * @param int        $depth    Perdiod count for each period types
   *
   * @return int[][] collection of arrays daily, weekly, monthly and yearly keys
   */
  function getPeriodicalStatsDetails($user_ids, $depth = 8) {
    $detail = array(
      "count" => 10,
      "weight" => 20000,
    );

    $sample = array_fill(0, $depth, $detail);
    return array(
      "hourly"  => $sample,
      "daily"   => $sample,
      "weekly"  => $sample,
      "monthly" => $sample,
      "yearly"  => $sample,
    );
  }

  /**
   * Return the patient
   *
   * @return CPatient|null
   */
  function loadRelPatient() {
    /** @var CPatient|IPatientRelated $object */
    $object = $this->loadTargetObject();
    if ($object instanceof CPatient) {
      return $object;
    }
    if (in_array("IPatientRelated", class_implements($object))) {
      return $object->loadRelPatient();
    }

    return null;
  }

  /**
   * @param string $sender   The sender's email address
   * @param string $receiver The receiver's email address
   *
   * @return string
   */
  function makeHprimHeader($sender, $receiver) {
    $object = $this->loadTargetObject();
    $receiver = explode('@', $receiver);
    $sender = explode('@', $sender);

    $patient = null;
    $record_id = null;
    $record_date = null;
    switch ($object->_class) {
      case 'CConsultation' :
        /** @var $object CConsultation  */
        $patient = $object->loadRefPatient();
        $object->loadRefSejour();
        if ($object->_ref_sejour) {
          $object->_ref_sejour->loadNDA();
          $record_id = $object->_ref_sejour->_NDA;
        }
        $object->loadRefPlageConsult();
        $record_date = $object->_ref_plageconsult->getFormattedValue('date');
        break;
      case 'CConsultAnesth' :
        /** @var $object CConsultAnesth */
        $patient = $object->loadRefPatient();
        $object->loadRefSejour();
        if ($object->_ref_sejour) {
          $object->_ref_sejour->loadNDA();
          $record_id = $object->_ref_sejour->_NDA;
        }
        $object->loadRefConsultation();
        $object->_ref_consultation->loadRefPlageConsult();
        $record_date = $object->_ref_consultation->_ref_plageconsult->getFormattedValue('date');
        break;
      case 'CSejour' :
        /** @var $object CSejour  */
        $patient = $object->loadRefPatient();
        $object->loadNDA();
        $record_id = $object->_NDA;
        $object->updateFormFields();
        $record_date = $object->getFormattedValue('_date_entree');
        break;
      case 'COperation' :
        /** @var $object COperation  */
        $patient = $object->loadRefPatient();
        $object->loadRefSejour();
        if ($object->_ref_sejour) {
          $object->_ref_sejour->loadNDA();
          $record_id = $object->_ref_sejour->_NDA;
        }

        /** Récupération de la date **/
        if ($object->date) {
          $record_date = $object->getFormattedValue('date');
        }
        else {
          $object->loadRefPlageOp();
          $record_date = $object->_ref_plageop->getFormattedValue('date');
        }
        break;
      case 'CPatient' :
        $patient = $object;
        break;
    }

    $patient->loadIPP();
    $adresse = explode("\n", $patient->adresse);

    if (count($adresse) == 1) {
      $adresse[1] = "";
    }
    elseif (count($adresse) > 2) {
      $adr_tmp = $adresse;
      $adresse = array($adr_tmp[0]);
      unset($adr_tmp[0]);
      $adr_tmp = implode(" ", $adr_tmp);
      $adresse[] = str_replace(array("\n", "\r"), array("", ""), $adr_tmp);
    }

    return $patient->_IPP . "\n"
    . strtoupper($patient->nom) . "\n"
    . ucfirst($patient->prenom) . "\n"
    . $adresse[0] . "\n"
    . $adresse[1] . "\n"
    . $patient->cp . " " . $patient->ville . "\n"
    . $patient->getFormattedValue("naissance") . "\n"
    . $patient->matricule . "\n"
    . $record_id . "\n"
    . $record_date . "\n"
    . ".          $sender[0]\n"
    . ".          $receiver[0]\n\n";
  }

  static function makeIconName($object) {
    switch ($object->_class) {
      default:
      case "CCompteRendu":
        $file_name = $object->nom;
        break;
      case "CFile":
        $file_name = $object->file_name;
        break;
      case "CExClass" :
        $file_name = $object->name;
    }

    $max_length = 25;

    if (strlen($file_name) <= $max_length) {
      return $object->_icon_name = $file_name;
    }

    return $object->_icon_name = substr_replace($file_name, " ... ", $max_length / 2, round(-$max_length / 2));
  }
}
