<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once dirname(__FILE__)."/../utils.php";
require_once "Task.class.php";

/**
 * Menu: Enable you to create a Menu of Tasks
 */
class Menu extends Task {
  /**
   * @var Task[]
   */
  public $task_list;

  public $name;

  /**
   * Constructor
   * 
   * @param string $name Name of the menu
   * 
   * @return void
   */
  function Menu($name) {
    $this->task_list = array();
    $this->name      = $name;
  }

  /**
   * Add a task to the menu
   * 
   * @param object $task Task name
   * 
   * @return void
   */
  function appendTask($task = null) {
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
   * @return Task[]
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
   * @return void
   */
  function showTasks() {
    foreach ( $this->getTaskList() as $nb => $oneTask ) {
      if ($nb !== 0) {
        echo sprintf("[%2d] %s\n", $nb, $oneTask->description);
      }
    }
  }
}
