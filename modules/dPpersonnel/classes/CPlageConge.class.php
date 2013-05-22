<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6194 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CPlageConge
 */
class CPlageConge extends CMbObject {
  // DB Table key
  public $plage_id;

  // DB Fields
  public $date_debut;
  public $date_fin;
  public $libelle;
  public $user_id;
  public $replacer_id;

  // Object References
  public $_ref_user;
  public $_ref_replacer;
  public $_ref_replacant;

  // Form fields
  public $_duree;

  // Behaviour fields
  public $_activite; // For pseudo plages

  /**
   * get specs
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $specs = parent::getSpec();
    $specs->table = "plageconge";
    $specs->key   = "plage_id";
    return $specs;
  }

  /**
   * class props
   *
   * @return array
   */
  function getProps() { 
    $specs = parent::getProps();
    $specs["user_id"]     = "ref class|CMediusers notNull";
    $specs["date_debut"]  = "date notNull";
    $specs["date_fin"]    = "date moreEquals|date_debut notNull";
    $specs["libelle"]     = "str notNull";
    $specs["replacer_id"] = "ref class|CMediusers";
    $specs["_duree"]      = "num";
    return $specs;
  }

  /**
   * backprops
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["replacement"] = "CReplacement conge_id";
    return $backProps;
  }

  /**
   * updateFormField
   *
   * @return null
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = $this->libelle;
  }

  /**
   * check before store
   *
   * @return null|string
   */
  function check() {
    $this->completeField("date_debut", "date_fin", "user_id");
    $plage_conge  = new CPlageConge();
    $plage_conge->user_id = $this->user_id;
    $plages_conge = $plage_conge->loadMatchingList();
    unset($plages_conge[$this->_id]);

    /** @var $plages_conge CPlageConge[] */
    foreach ($plages_conge as $_plage) {
      if (CMbRange::collides($this->date_debut, $this->date_fin, $_plage->date_debut, $_plage->date_fin)) {
        return CAppUI::tr("CPlageConge-conflit %s", $_plage->_view);
      }
    }
    return parent::check();
  }

  /**
   * loadFor
   *
   * @param string $user_id user_id
   *
   * @param string $date    date to check
   *
   * @return null
   */
  function loadFor($user_id, $date) {
    $where["user_id"] = "= '$user_id'";
    $where[] = "'$date' BETWEEN date_debut AND date_fin";
    $this->loadObject($where);
  }

  /**
   * load list for a range
   *
   * @param string $user_id user id
   * @param string $min     date min
   * @param string $max     date max
   *
   * @return CStoredObject[]
   */
  function loadListForRange($user_id, $min, $max) {
    $where["user_id"] = "= '$user_id'";
    $where["date_debut"] = "<= '$max'";
    $where["date_fin"  ] = ">= '$min'";
    $order = "date_debut";
    return $this->loadList($where, $order);
  }

  /**
   * LoadRefsReplacementFor, load the list of replacment for a user
   *
   * @param string $user_id user to check
   *
   * @param string $date    date
   *
   * @return CPlageConge[]
   */
  function loadRefsReplacementsFor($user_id, $date) {
    $where["replacer_id"] = "= '$user_id'";
    $where[] = "'$date' BETWEEN date_debut AND date_fin";
    return $this->loadList($where);
  }

  /**
   * load the user who is replaced
   *
   * @return CMediusers
   */
  function loadRefReplacer(){
    return $this->_ref_replacer = $this->loadUniqueBackRef("replacement");
  }

  /**
   * load the user replacing this->user
   *
   * @return CMediusers|null
   */
  function loadRefReplacant() {
    return $this->_ref_replacant = $this->loadFwdRef("replacer_id", true);
  }

  /**
   * load the user informations
   *
   * @return CMbObject|null
   */
  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    return $this->_ref_user;
  }

  /**
   * get perms
   *
   * @param int $permType permtype
   *
   * @return bool
   */
  function getPerm($permType) {
    if ($this->user_id == CAppUI::$user->_id) {
      return true;
    } 

    return $this->loadRefUser()->getPerm($permType);
  }

  /**
   * create a pseudoPlage
   *
   * @param string $user_id  user id
   *
   * @param string $activite activity type (deb or fin)
   *
   * @param string $limit    date limit chosen
   *
   * @return CPlageConge
   */
  static function makePseudoPlage($user_id, $activite, $limit) {
    // Parameter check
    if (!in_array($activite, array("deb", "fin"))) {
      CMbObject::error("Activite%s should be one of 'deb' or 'fin'", $activite);
    }

    // Make plage
    $plage = new self;
    $plage->_id = "$activite-$user_id";
    $plage->user_id = $user_id;
    $plage->_activite = $activite;
    $plage->libelle   = CAppUI::tr("$plage->_class._activite.$activite");

    // Concerned user
    $user = CMediusers::get($user_id);

    // Dates for deb case
    if ($activite == "deb") {
      $plage->date_debut = $limit;
      $plage->date_fin = CMbDT::date("-1 DAY", $user->deb_activite);
    }

    // Dates for fin case
    if ($activite == "fin") {
      $plage->date_debut = CMbDT::date("+1 DAY", $user->fin_activite);
      $plage->date_fin   = $limit;
    }

    return $plage;
  }
}