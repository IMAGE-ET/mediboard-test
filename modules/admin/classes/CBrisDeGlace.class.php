<?php 

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

class CBrisDeGlace extends CMbMetaObject {
  public $bris_id;

  public $date;
  public $user_id;
  public $comment;

  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'bris_de_glace';
    $spec->key   = 'bris_id';
    return $spec;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]           = "ref class|CMediusers notNull";
    $props["date"]              = "dateTime notNull";
    $props["group_id"]          = "ref class|CGroups notNull";
    $props["comment"]           = "text notNull helped";
    return $props;
  }

  /**
   * @return CMediusers|null
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", true);
  }

  /**
   * check if the sejour need to be unlock
   *
   * @param CSejour $sejour
   *
   * @return bool
   */
  static function checkForSejour($sejour, $modal = true) {
    if (!self::canAccess($sejour)) {
      $smarty = new CSmartyDP("modules/admin");
      $smarty->assign("sejour", $sejour);
      $smarty->assign("bris", new CBrisDeGlace());
      $smarty->assign("modale", $modal);
      $smarty->display("need_bris_de_glace.tpl");
      CApp::rip();
    }

    return true;
  }


  static function isBrisDeGlaceRequired() {
    return CAppUI::conf("admin CBrisDeGlace enable_bris_de_glace", CGroups::loadCurrent()) && CAppUI::$user->use_bris_de_glace;
  }

  /**
   * check if we can access to the view following the configuration and already granted.
   *
   * @param CSejour $sejour sejour object
   *
   * @return bool
   */
  static function canAccess($sejour) {
    $group = $sejour->loadRefEtablissement();
    $user = CMediusers::get();

    //check for config and elements
    if (
      !$sejour->_id ||
      !CAppUI::conf("admin CBrisDeGlace enable_bris_de_glace", $group) ||
      ($sejour->praticien_id == $user->_id) ||
      !$user->use_bris_de_glace) {
      return true;
    }

    $today = CMbDT::date();

    $bris = new self();
    $where = array();
    $where["date"] = " BETWEEN '$today 00:00:00' AND '$today 23:59:59'";
    $where["object_class"] = " = 'CSejour'";
    $where["object_id"] = " = '$sejour->_id'";
    $where["user_id"] = " = '$user->_id'";

    // no need of bris de glace
    if ($bris->countList($where)) {
      return true;
    }

    return false;
  }

  /**
   *
   *
   * @param null|int    $user_id
   * @param null|string $date_start
   * @param null|string $date_end
   * @param array       $object_classes
   *
   * @return CBrisDeGlace[] $briss
   */
  static function loadBrisForUser($user_id = null, $date_start = null, $date_end = null, $object_classes = array()) {
    $date_start = $date_start ? $date_start : CMbDT::date();
    $date_end = $date_end ? $date_end : $date_start;
    $bris = new self();
    $ds = $bris->getDS();
    $where = array();
    $where["date"] = " BETWEEN '$date_start 00:00:00' AND '$date_end 23:59:59' ";
    if (count($object_classes)) {
      $where["object_class"] = $ds->prepareIn($object_classes);
    }
    if ($user_id) {
      $where["user_id"] = " = '$user_id'";
    }

    /** @var CBrisDeGlace[] $briss */
    $briss = $bris->loadList($where, "date DESC");
    return $briss;
  }

  /**
   * load the sejours managed by user_id which has been broken by other
   *
   * @param null $user_id
   * @param array $object_classes
   * @param null $date_start
   * @param null $date_end
   *
   * @return CBrisDeGlace[]
   */
  static function loadBrisForOwnObject($user_id = null, $object_classes = array(), $date_start = null, $date_end = null) {
    $date_start = $date_start ? $date_start : CMbDT::date();
    $date_end = $date_end ? $date_end : $date_start;
    $user_id = $user_id ? $user_id : CMediusers::get()->_id;

    $bris = new CBrisDeGlace();
    $ljoin = array("sejour" => "sejour.sejour_id = bris_de_glace.object_id");
    $where = array(
      "bris_de_glace.object_class" => " = 'CSejour' ",
      "sejour.praticien_id" => " =  '$user_id' ",
      "bris_de_glace.user_id" => " != '$user_id' ",
      "bris_de_glace.date" => " BETWEEN '$date_start 00:00:00' AND '$date_end 23:59:59' "
    );

    /** @var CBrisDeGlace[] $briss */
    $briss = $bris->loadList($where, "date DESC", null, null, $ljoin);
    return $briss;
  }
}