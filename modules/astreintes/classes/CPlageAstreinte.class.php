<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6194 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
class CPlageAstreinte extends CMbObject {
  // DB Table key
  var $plage_id = null;

  // DB Fields
  var $date_debut  = null;
  var $date_fin    = null;
  var $libelle     = null;
  var $user_id     = null;

  // Object References
  var $_num_astreinte  = null;
  var $_ref_user       = null;


  // Form fields
  var $_duree      = null;
  var $_type      = null;

  // Behaviour fields

  function getSpec() {
    $specs = parent::getSpec();
    $specs->table = "plage_astreinte";
    $specs->key   = "plage_id";
    return $specs;
  }

  //spécification des propriétés
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]         = "ref class|CMediusers notNull";
    $specs["_ref_user"]       = "ref class|CMediusers";
    $specs["date_debut"]      = "date notNull";
    $specs["date_fin"]        = "date moreEquals|date_debut notNull";
    $specs["libelle"]         = "str";
    $specs["_duree"]          = "num";
    $specs["_num_astreinte"]  = "phone";
    $specs["_type"]           = "enum list|medical|admin";
    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }

  function loadView() {
    parent::loadView();
    $this->_ref_user = $this->loadRefUser();  //I need the Phone Astreinte
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = $this->libelle;
  }

  function check() {
    $this->completeField("date_debut", "date_fin", "user_id");
    $plage_astreinte  = new CPlageAstreinte();
    $plage_astreinte->user_id = $this->user_id;
    $plages_astreinte = $plage_astreinte->loadMatchingList();
    unset($plages_astreinte[$this->_id]);

    foreach($plages_astreinte as $_plage) {
      if (CMbRange::collides($this->date_debut, $this->date_fin, $_plage->date_debut, $_plage->date_fin)) {
        return CAppUI::tr("CPlageConge-conflit %s", $_plage->_view);
      }
    }
    return parent::check();
  }

  function loadFor($user_id, $date) {
    $where["user_id"] = "= '$user_id'";
    $where[] = "'$date' BETWEEN date_debut AND date_fin";
    $this->loadObject($where);
  }

  function loadListForRange($user_id, $min, $max) {
    $where["user_id"] = "= '$user_id'";
    $where["date_debut"] = "<= '$max'";
    $where["date_fin"  ] = ">= '$min'";
    $order = "date_debut";
    return $this->loadList($where, $order);
  }

  function loadDays($debut, $fin) {
    $this->_duree = CMbDT::daysRelative($date_debut,$date_fin);
  }

  function loadType() {
    if($this->_ref_user->isMedical()) {
      $_plage->_type = 'medical';
    }

    if($this->_ref_user->isSecretaire()) {
      $_plage->_type = 'admin';
    }

    return $_plage->_type;
  }

  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    $this->_num_astreinte = $this->_ref_user->_user_astreinte;
    $this->_type = $this->_ref_user->_user_type;
    return $this->_ref_user;
  }

  function loadRefPhoneAstreinte() {
    $this->_num_astreinte = $this->loadFwdRef("_user_astreinte", true);
    return $this->_num_astreinte;
  }

  function getPerm($permType) {
    if ($this->user_id == CAppUI::$user->_id) {
      return true;
    }

    return $this->loadRefUser()->getPerm($permType);
  }

  /**
   * Make a pseudo plage corresponding to activity deb/fin for given user
   *
   * @param ref[CUser] $user_id  User
   * @param string     $type     Either deb or fin
   * @param date       $limit    Limit date to build pseudo plage
   * @return CPlageConge
   */
  static function makePseudoPlage($user_id, $activite, $limit) {
    // Parameter check
    if (!in_array($activite, array("deb", "fin"))) {
      trigger_error("Activite '$activite' should be one of 'deb' or 'fin'", E_USER_WARNING);
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
?>