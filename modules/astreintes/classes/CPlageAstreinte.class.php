<?php /** $Id **/

/**
 * CPlageAstreinte class
 *
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  Release: <package_version>
 * @link     http://www.mediboard.org
 */
class CPlageAstreinte extends CPlageCalendaire {
  // DB Table key
  public $plage_id;

  // DB Fields
  public $libelle;
  public $user_id;
  public $group_id;
  public $type;
  public $phone_astreinte;

  // available types
  static $astreintes_type = array(
    "medical",
    "admin",
    "personnelsoignant"
  );

  // Object References
  public $_num_astreinte;

  /** @var CMediusers $_ref_user */
  public $_ref_user;
  /** @var CGroups $_ref_group  */
  public $_ref_group;

  // Form fields
  public $_duree;   //00:00:00
  public $_hours;   // 29.5 hours
  public $_duration;
  public $_color;
  public $_font_color;

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
    $specs["group_id"]        = "ref class|CGroups notNull";
    $specs["phone_astreinte"] = "str notNull";
    return $specs;
  }

  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
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

    if (CModule::getCanDo('astreintes')->edit && $this->_ref_user->getPerm($permType)) {
      return true;
    }

    /* @todo À quoi sert ce droit ? */
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
    return $this->_duree = CMbDate::duration($this->start, $this->end, 0);
  }

  /**
   * get the number of hours between start & end
   *
   * @return float
   */
  function getHours() {
    return $this->_hours = CMbDT::minutesRelative($this->start, $this->end)/60;
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
    $color = CAppUI::conf("astreintes astreinte_".$this->type."_color");

    $this->_font_color = CColorSpec::get_text_color($color) > 130 ? '000000' :  'ffffff';

    return $this->_color = CAppUI::conf("astreintes astreinte_".$this->type."_color");
  }

  /**
   * load ref user
   *
   * @return CMediusers
   */
  function loadRefUser() {
    /** @var CMediusers $user */
    $mediuser = $this->loadFwdRef("user_id", true);
    $mediuser->loadRefFunction();

    $this->_num_astreinte = $mediuser->_user_astreinte;

    return $this->_ref_user = $mediuser;
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
