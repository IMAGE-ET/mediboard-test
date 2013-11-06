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
 * Permet d'ajouter des observations médicales à un séjour
 */
class CObservationMedicale extends CMbMetaObject {

  // DB Table key
  public $observation_medicale_id;
  
  // DB Fields
  public $sejour_id;
  public $user_id;
  
  public $degre;
  public $date;
  public $text;
  public $type;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CMediusers */
  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'observation_medicale';
    $spec->key   = 'observation_medicale_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]    = "ref class|CMbObject meta|object_class cascade";
    $specs["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineMix show|0";
    $specs["sejour_id"]    = "ref notNull class|CSejour";
    $specs["user_id"]      = "ref notNull class|CMediusers";
    $specs["degre"]        = "enum notNull list|low|high|info default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["text"]         = "text helped|degre";
    $specs["type"]         = "enum list|reevaluation";
    return $specs;
  }

  /**
   * @see parent::canEdit()
   */
  function canEdit(){
    $nb_hours = CAppUI::conf("soins max_time_modif_suivi_soins");
    $datetime_max = CMbDT::dateTime("+ $nb_hours HOURS", $this->date);
    return $this->_canEdit = (CMbDT::dateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);
  }

  /**
   * Charge le séjour
   *
   * @return CSejour
   */
  function loadRefSejour(){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Charge l'utilisateur
   *
   * @return CMediusers
   */
  function loadRefUser(){
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
    $this->_view = "Observation du Dr ".$this->_ref_user->_view;
  }

  /**
   * @see parent::check()
   */
  function check(){
    if (!$this->_id && $this->degre == "info" && $this->text == "Visite effectuée") {
      if ($this->countNotifSiblings()) {
        return "Notification deja effectuée";
      }
    }
    return parent::check();
  }

  /**
   * Compte les visites effectuées
   *
   * @return int
   */
  function countNotifSiblings() {
    $date = CMbDT::date($this->date);
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->sejour_id'";
    $where["user_id"]  = " = '$this->user_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    $where["text"] = " = 'Visite effectuée'";
    return $observation->countList($where);
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
  
}

