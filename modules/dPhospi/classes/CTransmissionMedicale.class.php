<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CTransmissionMedicale
 *
 * @property CPrescriptionLine|CCategoryPrescription|CAdministration _ref_object
 */
class CTransmissionMedicale extends CMbMetaObject implements IIndexableObject {
  // DB Table key
  public $transmission_medicale_id;

  // DB Fields
  public $sejour_id;
  public $user_id;
  public $degre;
  public $date;
  public $date_max;
  public $text;
  public $type;
  public $libelle_ATC;
  public $locked;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CMediusers */
  public $_ref_user;

  /** @var CPrescriptionLine|CCategoryPrescription|CAdministration */
  public $_ref_cible;

  // Form fields
  public $_cible;
  public $_text_data;
  public $_text_action;
  public $_text_result;
  public $_log_lock;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"]    = "ref class|CMbObject meta|object_class nullify";
    $props["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration|CPrescriptionLineMix show|0";
    $props["sejour_id"]    = "ref notNull class|CSejour";
    $props["user_id"]      = "ref notNull class|CMediusers";
    $props["degre"]        = "enum notNull list|low|high default|low";
    $props["date"]         = "dateTime notNull";
    $props["date_max"]     = "dateTime";
    $props["text"]         = "text helped|type|object_id";
    $props["type"]         = "enum list|data|action|result";
    $props["libelle_ATC"]  = "text";
    $props["locked"]       = "bool default|0";
    $props["_text_data"]   = "text helped|type|object_id";
    $props["_text_action"] = "text helped|type|object_id";
    $props["_text_result"] = "text helped|type|object_id";
    return $props;
  }

  /**
   * Charge le séjour
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Charge l'utilisateur
   *
   * @return CMediusers
   */
  function loadRefUser() {
    /** @var CMediusers $user */
    $user = $this->loadFwdRef("user_id", true);
    $user->loadRefFunction();
    return $this->_ref_user = $user;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
    $this->_view = "Transmission de ".$this->_ref_user->_view;
  }

  /**
   * @see parent::canEdit()
   */
  function canEdit(){
    $nb_hours = CAppUI::conf("soins Other max_time_modif_suivi_soins", CGroups::loadCurrent()->_guid);
    $datetime_max = CMbDT::dateTime("+ $nb_hours HOURS", $this->date);
    return $this->_canEdit = (CMbDT::dateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);
  }

  function calculCibles(&$cibles = array()){
    $state = $this->locked ? "closed" : "opened";
    if ($this->object_id && $this->object_class) {
      // Ligne de medicament, cible => classe ATC
      if ($this->object_class == "CPrescriptionLineMedicament") {
        $libelle_ATC = $this->_ref_object->_ref_produit->_ref_ATC_2_libelle;
        $this->_cible = $libelle_ATC;
        if (!isset($cibles["opened"][$libelle_ATC]) &&
            !isset($cibles["closed"][$libelle_ATC])
        ) {
          $cibles[$state][$libelle_ATC] = $libelle_ATC;
        }
      }

      // Ligne d'element, cible => categorie
      if ($this->object_class == "CPrescriptionLineElement") {
        $category = $this->_ref_object->_ref_element_prescription->_ref_category_prescription;
        $this->_cible = $category->_view;
        if (!isset($cibles["opened"][$category->_id]) &&
            !isset($cibles["closed"][$category->_id])
        ) {
          $cibles[$state][$category->_view] = $category->_view;
        }
      }

      // Administration => ATC ou categorie
      if ($this->object_class == "CAdministration") {
        if ($this->_ref_object->object_class == "CPrescriptionLineMedicament") {
          $this->_ref_object->loadTargetObject();
          $libelle_ATC = $this->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle;
          $this->_cible = $libelle_ATC;

          if (!isset($cibles["opened"][$libelle_ATC]) &&
              !isset($cibles["closed"][$libelle_ATC])
          ) {
            $cibles[$state][$libelle_ATC] = $libelle_ATC;
          }
        }
        if ($this->_ref_object->object_class == "CPrescriptionLineElement") {
          $this->_ref_object->loadTargetObject();
          $category = $this->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription;
          $this->_cible = $category->_view;
          if (!isset($cibles["opened"][$category->_id]) &&
              !isset($cibles["closed"][$category->_id])
          ) {
            $cibles[$state][$category->_view] = $category->_view;
          }
        }
      }

      if ($this->object_class == "CCategoryPrescription") {
        $this->_cible = $this->_ref_object->_view;
        if (!isset($cibles["opened"][$this->object_id]) &&
            !isset($cibles["closed"][$this->object_id])
        ) {
          $cibles[$state][$this->_ref_object->_view] = $this->_ref_object->_view;
        }
      }

      if ($this->object_class == "CPrescriptionLineMix") {
        $this->_cible = "Perfusion";
        if (!isset($cibles["opened"]["Perfusion"]) && !isset($cibles["closed"]["Perfusion"])) {
          $cibles[$state]["Perfusion"] = "Perfusion";
        }
      }
    }

    if ($this->libelle_ATC) {
      $this->_cible = $this->libelle_ATC;
      if ((!isset($cibles["opened"]["ATC"]) && !isset($cibles["closed"]["ATC"])) ||
          (!@in_array($this->libelle_ATC, $cibles["opened"]["ATC"]) && @!in_array($this->libelle_ATC, $cibles["closed"]["ATC"]))
      ) {
        $cibles[$state]["ATC"][$this->libelle_ATC] = $this->libelle_ATC;
      }
    }
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Si une cible est définie, on Unlock la précédente transmission sur la même cible
    // (classe ATC ou catégorie de prescription)
    $this->completeField("sejour_id", "libelle_ATC", "object_id", "object_class");

    if ($this->libelle_ATC || ($this->object_id && $this->object_class)) {
      $trans = new CTransmissionMedicale();
      if ($this->libelle_ATC) {
        $trans->libelle_ATC = $this->libelle_ATC;
      }
      else if ($this->object_id && $this->object_class) {
        $trans->object_class = $this->object_class;
        $trans->object_id    = $this->object_id;
      }
      $trans->sejour_id    = $this->sejour_id;
      $trans->locked       = 1;
      $trans->loadMatchingObject("transmission_medicale_id DESC");

      if ($trans->_id && $trans->_id != $this->_id) {
        $trans->locked = 0;
        $trans->store();
      }
    }
    return parent::store();
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($perm) {
    if (!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return CPatient
   */
  function getIndexablePatient () {
    $this->loadRefSejour();
    $this->_ref_sejour->loadRelPatient();
    return $this->_ref_sejour->_ref_patient;
  }
  /**
   * Loads the related fields for indexing datum (patient_id et date)
   *
   * @return array
   */
  function getIndexableData () {
    $prat = $this->getIndexablePraticien();
    $array["id"]          = $this->_id;
    $array["author_id"]   = $this->user_id;
    $array["prat_id"]     = $prat->_id;
    $array["title"]       = $this->type;
    $array["body"]        = $this->text;
    $array["date"]        = str_replace("-", "/", $this->date);
    $array["function_id"] = $prat->function_id;
    $array["group_id"]    = $prat->loadRefFunction()->group_id;
    $array["patient_id"]  = $this->getIndexablePatient()->_id;
    $array["object_ref_id"]  = $this->loadRefSejour()->_id;
    $array["object_ref_class"]  = $this->loadRefSejour()->_class;

    return $array;
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody ($content) {
    return $content;
  }

  /**
   * Get the praticien_id of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien () {
    return $this->loadRefSejour()->loadRefPraticien();
  }
}
