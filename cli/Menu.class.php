<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once "utils.php";
require_once "Task.class.php";

/**
 * Menu: Enable you to create a Menu of Tasks
 */
class Menu extends Task
{
  public $task_list;

  public $name;

  /**
   * Constructor
   * 
   * @param object $name Name of the menu
   * 
   * @return 
   */
  function Menu( $name ) {
    $this->task_list  = array();
    $this->name       = $name;
  }

  /**
   * Add a task to the menu
   * 
   * @param object $task [optional] Task name
   * 
   * @return None
   */
  function appendTask( $task = null ) {
    if ( $task instanceof Task ) {
      $this->task_list[] = $task;
    }
    else {
      cecho("Given parameter is not a Task object.", "red", "bold");
      echo "\n";
    }
  }

  /**
   * Get the list of all the tasks
   * 
   * @param object $id [optional] If specified, get the task which matches with $id
   * 
   * @return array
   */
  function getTaskList($id = null) {
    if ( is_null($id) ) {
      return $this->task_list;
    }
    else {
      return $this->task_list[$id];
    }
  }

  /**
   * Show the tasks on the screen, except the first ($id = 0)
   * 
   * @return None
   */
  function showTasks() {
    foreach ( $this->getTaskList() as $nb => $oneTask ) {
      if ($nb !== 0) {
        echo "[".$nb."] ".$oneTask->description."\n";
      }
    }
  }

}

?>