<?php

/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

/**
 * Mediusers statistics view class
 */
class CMediusersStats {
  public $date;
  public $dates;
  public $min_date;
  public $max_date;
  public $period;
  public $php_period;
  public $sql_date;
  public $totals = array();

  /**
   * Standard constructor
   *
   * @param date   $date        Reference ISO date
   * @param string $period      One of day, week, month, year
   * @param string $date_column SELECT-like column, might be a expression such as DATE(when)
   */
  public function __construct($date, $period, $date_column) {
    // Prepare periods
    switch ($period) {
      case "day": 
        $php_period = "days";
        $sql_date = "$date_column";
        break;
      case "week":
        $date = CMbDT::date("next monday", $date);
        $php_period = "weeks";
        $sql_date = "DATE_ADD($date_column, INTERVAL (2 - DAYOFWEEK($date_column)) DAY)";
        break;
      case "month":
        $date = CMbDT::date("first day of +0 month", $date);
        $php_period = "months";
        $sql_date = "DATE_ADD($date_column, INTERVAL (1 - DAYOFMONTH($date_column)) DAY)";
        break;
      case "year":
        $date = CMbDT::transform(null, $date, "%Y-01-01");
        $php_period = "years";
        $sql_date = "DATE_ADD($date_column, INTERVAL (1 - DAYOFYEAR($date_column)) DAY)";
        break;
      default:
        $php_period = null;
        $min_date = null;
        $sql_date = null;
        break;
    } 
    
    // Prepare dates
    $dates = array();
    foreach (range(0, 29) as $n) {
      $dates[] = $min_date = CMbDT::date("- $n $php_period", $date);
    }
    $dates = array_reverse($dates);

    $min_date = reset($dates);
    $max_date = CMbDT::date("+1 $this->period -1 day", end($dates));

    // Members
    $this->date       = $date;
    $this->period     = $period;
    $this->dates      = $dates;
    $this->min_date   = $min_date;
    $this->max_date   = $max_date;
    $this->php_period = $php_period;
    $this->sql_date   = $sql_date;
  }

  /**
   * Add a total for a user at a given date
   *
   * @param int   $user_id CMediuser id
   * @param date  $date    Date for total
   * @param mixed $value   Value for total
   *
   * @return void
   */
  function addTotal($user_id, $date, $value) {
    if (!in_array($date, $this->dates)) {
      $warning = CAppUI::tr("CMediusersStats-warning-total_incorrect_date", $date);
      trigger_error($warning, E_USER_WARNING);
    }
    
    if (isset($this->totals[$user_id][$date])) {
      $warning = CAppUI::tr("CMediusersStats-warning-already_defined", $user_id, $date);
      trigger_error($warning, E_USER_WARNING);
    }

    $this->totals[$user_id][$date] = $value;
  }

  /**
   * Display the total matrix templage
   *
   * @param string $title Locale string for template title
   *
   * @return void
   */
  function display($title) {
    // Prepare groups-functions-users hierarchy
    $user = CMediusers::get();

    /** @var CMediusers[] $users     */
    /** @var CFunctions[] $functions */
    /** @var CGroups[]    $groups    */
    $users     = $user->loadAll(array_keys($this->totals));
    $functions = CStoredObject::massLoadFwdRef($users, "function_id");
    $groups    = CStoredObject::massLoadFwdRef($functions, "group_id");
        
    foreach ($users as $_user) {
      $_user->loadRefFunction()->loadRefGroup();
      
      // Function-users linkage
      $function = $functions[$_user->function_id];
      $function->_ref_users[$_user->_id] = $_user;

      // Group-functions linkage
      $group = $groups[$function->group_id];
      $group->_ref_functions[$function->_id] = $function;
    }
    
    // Display the template
    $smarty = new CSmartyDP();
    
    $smarty->assign("period"   , $this->period);
    $smarty->assign("dates"    , $this->dates );
    $smarty->assign("min_date" , $this->min_date);
    $smarty->assign("max_date" , $this->max_date);
    $smarty->assign("totals"   , $this->totals);
    $smarty->assign("users"    , $users );
    $smarty->assign("functions", $functions);
    $smarty->assign("groups"   , $groups);
    $smarty->assign("title"    , $title);
    
    $smarty->display("../../../modules/mediusers/templates/user_stats.tpl");
  }
}
