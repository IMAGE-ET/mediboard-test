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
  public $period;
  public $dates;
  public $php_period;
  public $sql_date;
  public $totals = array();
  
  public function __construct($date, $period) {
    // Prepare periods
    switch ($period) {
      case "day": 
        $php_period = "days";
        $sql_date = "date";
        break;
      case "week":
        $date = CMbDT::date("next monday", $date);
        $php_period = "weeks";
        $sql_date = "DATE_ADD( date, INTERVAL (2 - DAYOFWEEK(date)) DAY)";
        break;
      case "month":
        $date = CMbDT::date("first day of +0 month", $date);
        $php_period = "months";
        $sql_date = "DATE_ADD( date, INTERVAL (1 - DAYOFMONTH(date)) DAY)";
        break;
      case "year":
        $date = CMbDT::transform(null, $date, "%Y-01-01");
        $php_period = "years";
        $sql_date = "DATE_ADD( date, INTERVAL (1 - DAYOFYEAR(date)) DAY)";
        break;
    } 
    
    // Prepare dates
    $dates = array();
    foreach (range(0, 29) as $n) {
      $dates[] = $min_date = CMbDT::date("- $n $php_period", $date);
    }
    $dates = array_reverse($dates);

    // Members
    $this->date       = $date;
    $this->period     = $period;
    $this->dates      = $dates;
    $this->min_date   = $min_date;
    $this->php_period = $php_period;
    $this->sql_date   = $sql_date;
  }

  function addTotal($user_id, $date, $total) {
    if (!in_array($date, $this->dates)) {
      $warning = CAppUI::tr("CMediusersStats-warning-total_incorrect_date", $date);
      trigger_error($warning, E_USER_WARNING);
    }
    
    if (isset($this->totals[$user_id][$date])) {
      $warning = CAppUI::tr("CMediusersStats-warning-already_defined", $user_id, $date);
      trigger_error($warning, E_USER_WARNING);
    }

    
    $this->totals[$user_id][$date] = $total;
  }
  
  function display($title) {
    // Prepare groups-functions-users hierarchy
    $user = CMediusers::get();
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
    $smarty->assign("min_date" , reset($this->dates));
    $smarty->assign("max_date" , CMbDT::date("+1 $this->period -1 day", end($this->dates)));
    $smarty->assign("totals"   , $this->totals);
    $smarty->assign("users"    , $users );
    $smarty->assign("functions", $functions);
    $smarty->assign("groups"   , $groups);
    $smarty->assign("title"    , $title);
    
    $smarty->display("../../../modules/mediusers/templates/user_stats.tpl");
  }
}
