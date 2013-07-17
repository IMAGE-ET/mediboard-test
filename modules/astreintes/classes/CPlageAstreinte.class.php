<?php /** $Id **/

/**
 * CPlageAstreinte class
 *
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
class CPlageAstreinte extends CPlageCalendaire {
  // DB Table key
  public $plage_id;

  // DB Fields
  public $libelle;
  public $user_id;
  public $type;
  public $phone_astreinte;


  static $astreintes_type = array(
    "medical",
    "admin",
    "personnelsoignant"
  );
  // Object References
  public $_num_astreinte;
  /** @var  CMediusers */
  public $_ref_user;
  public $_type;


  // Form fields
  public $_duree;   //00:00:00
  public $_hours;   // 29.5 hours
  public $_duration;
  public $_color;

  /** Behaviour fields
   *
   * @return string $specs
   *
   */
  function getSpec() {
    $specs = parent::getSpec();
    $specs->table = "astreinte_plage";
    $specs->key   = "plage_id";
    $specs->collision_keys = array("type", "user_id");
    return $specs;
  }

  /**
   * spécification des propriétés
   *
   * @return array $specs
   **/
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]         = "ref class|CMediusers notNull";
    $specs["type"]           = "enum list|".implode("|", self::$astreintes_type)." notNull";
    $specs["libelle"]         = "str";
    $specs["phone_astreinte"] = "phone notNull";
    return $specs;
  }

  /**
   * get backprops
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }

  /**
   * loadView
   *
   * @return null
   */
  function loadView() {
    parent::loadView();
    $this->getDuree();
    $this->getDuration();
    $this->_ref_user = $this->loadRefUser();  //I need the Phone Astreinte
  }

  /**
   * load list of Astreinte for a specified range
   *
   * @param int    $user_id user_id
   * @param string $min     date min
   * @param string $max     date max
   *
   * @return CStoredObject[]
   */
  function loadListForRange($user_id, $min, $max) {
    $where["user_id"] = "= '$user_id'";
    $where["start"] = "<= '$max'";
    $where["end"  ] = ">= '$min'";
    $order = "end";
    return $this->loadList($where, $order);
  }

  /**
   * get the permission type
   *
   * @param int $permType permission type
   *
   * @return bool
   */
  function getPerm($permType) {
    if (CAppUI::$user->isAdmin()) {
      return true;
    }

    if ($this->_ref_user->_id == CAppUI::$user->_id) {
      return true;
    }

    if (CModule::getCanDo('astreintes')->edit && $this->_ref_user->getPerm($permType)) {
      return true;
    }

    if (CModule::getCanDo("astreintes")->read && $permType <= PERM_READ) {
      return true;
    }

    return false;
  }

  /**
   * get the duration
   *
   * @return string
   */
  function getDuree() {
    $duree = CMbDate::duration($this->start, $this->end, 0);
    return $this->_duree = $duree;
  }

  /**
   * get the number of hours between start & end
   *
   * @return float
   */
  function getHours() {
    $duree = CMbDT::minutesRelative($this->start, $this->end)/60;
    return $this->_hours = $duree;
  }

  /**
   * get duration for the current plage
   *
   * @return array
   */
  function getDuration() {
    return $this->_duration = CMbDate::duration($this->start, $this->end);
  }

  /**
   * load color for astreinte
   *
   * @return mixed
   */
  function loadRefColor() {
    return $this->_color = CAppUI::conf("astreintes astreinte_".$this->type."_color");
  }

  /**
   * load ref user
   *
   * @return CMbObject
   */
  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    $this->_num_astreinte = $this->_ref_user->_user_astreinte;
    $this->_type = $this->_ref_user->_user_type;
    return $this->_ref_user;
  }

  /**
   * load phone for astreinte
   *
   * @return CMbObject
   */
  function loadRefPhoneAstreinte() {
    return $this->_num_astreinte = $this->loadFwdRef("_user_astreinte", true);
  }
}
