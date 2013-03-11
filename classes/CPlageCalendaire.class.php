<?php /** $Id */

/**
 * Class usef for calendar duration
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  Release: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
class CPlageCalendaire extends CMbObject{

  public $start;
  public $end;

  public $_duration;

  /**
   * @var self[]
   */
  public $_colliding_plages;
  public $_collisionList = array();


  /**
   * getprops
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["start"]   = "dateTime notNull";
    $props["end"]     = "dateTime notNull moreThan|start";
    return $props;
  }


  /**
   * get spec
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();

    // Collision keys mandatory definition to determine which references identify collisions
    // Has to be an array, be it empty
    $spec->collision_keys = null;

    return $spec;
  }


  /**
   * Check collision with another plage regarding defined in class spec
   *
   * @return string Collision message
   */
  function hasCollisions() {
    $collisions = self::getCollisions();
    return count($collisions) ? implode(", ", $collisions) : null;
  }


  /**
   * Check collision with another plage regarding defined in class spec
   *
   * @return array Collision list
   */
  function getCollisions() {
    // Check whether mandatory collision keys are defined
    $keys = $this->_spec->collision_keys;
    if (!is_array($keys)) {
      CModelObject::error("class%s-collision_keys-not-available", $this->_class);
      return;
    }

    $keys = $this->_spec->collision_keys;
    $this->completeField("start", "end");
    $this->completeField($keys);

    // Get all other plages the same day
    //chevauchement & inside
    $where[$this->_spec->key]   = "!= '$this->_id'";
    $where[] = "(`start` < '$this->start' AND `end` > '$this->start') OR
    (`start` < '$this->end' AND `end` > '$this->end') OR
    (`start` > '$this->end' AND `end` < '$this->end')";

    // Append collision keys clauses
    foreach ($keys as $_key) {
      $where[$_key] = "= '{$this->$_key}'";
    }

    // Load collision
    $plages = new $this->_class;
    $this->_colliding_plages = $plages->loadList($where);

    // Build collision message
    $msgs = array();
    foreach ($this->_colliding_plages as $_plage) {
      $msgs[] = CAppUI::tr("CPlageCalendaire-collision-with-plageNb%d-start%s-end%s", $_plage->_id, $_plage->start, $_plage->end);
    }

    return $this->_collisionList = $msgs;
  }

  /**
   * store
   *
   * @return null|string
   */
  function store() {
    if ($msg = $this->hasCollisions()) {
      return $msg;
    }

    return parent::store();
  }
}