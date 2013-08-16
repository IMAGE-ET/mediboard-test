<?php

/**
 * CPlanningMonth class
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


/**
 * Class CPlanning
 */
class CPlanningMonth extends CPlanning {


  /**
   * constructor
   *
   * @param string $date current date in the planning
   */
  /**
   * constructor
   *
   * @param string $date        current date in the planning
   * @param null   $date_min    min date of the planning
   * @param null   $date_max    max
   * @param bool   $selectable  is the planning selectable
   * @param string $height      [optional] height of the planning, default : auto
   * @param bool   $large       [optional] is the planning a large one
   * @param bool   $adapt_range [optional] can the planning adapt the range
   */
  function __construct($date, $date_min = null, $date_max = null, $selectable = false, $height = "auto", $large = false, $adapt_range = false) {
    parent::__construct($date);
    $this->type = "month";
    $this->selectable = $selectable;
    $this->height = $height ? $height : "auto";
    $this->large = $large;
    $this->adapt_range = $adapt_range;

    $this->no_dates = true;

    if (is_int($date) || is_int($date_min) || is_int($date_max)) {
      $this->no_dates = true;
      $this->date_min = $this->date_min_active = $this->_date_min_planning = $date_min;
      $this->date_max = $this->date_max_active = $this->_date_max_planning = $date_max;
      $this->nb_days = (CMbDT::transform(null, $this->date_max, "%d") - CMbDT::transform(null, $this->date_min, "%d"));

      for ($i = 0 ; $i < $this->nb_days ; $i++) {
        $this->days[$i] = array();
        $this->load_data[$i] = array();
      }
    }
    else {
      $this->date_min = $this->date_min_active = $this->_date_min_planning = CMbDT::date("first day of this month"   , $date);
      $this->date_max = $this->date_max_active = $this->_date_max_planning = CMbDT::date("last day of this month", $this->date_min);

      $this->nb_days = CMbDT::transform(null, $this->date_max, "%d");
      for ($i = 0; $i < $this->nb_days; $i++) {
        $_day = CMbDT::date("+$i day", $this->date_min);
        $this->days[$_day] = array();
        $this->load_data[$_day] = array();
      }
    }



    $this->_date_min_planning = reset(array_keys($this->days));
    $this->_date_max_planning = end(array_keys($this->days));

    $this->_hours = array();
  }

}
