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
require_once "Question.class.php";
require_once "Menu.class.php";

/**
 * Task: Enable you to create a Task, which is associated to a Procedure
 */
class Task {
  
  public $procedure   = null;
  public $description = null;
  
  /**
   * Constructor
   * 
   * @param string $procedure   The name of the procedure function associated to the Task, must be callable
   * @param object $description The Task description
   * 
   * @return 
   */
  function Task( $procedure, $description ) {
    $this->procedure      = $procedure;
    $this->description    = $description;
  }
  
  /**
   * To create a Menu object
   * 
   * @param string $name The name of the Menu
   * 
   * @return object
   */
  function createMenu( $name ) {
    return new Menu($name);
  }
  
  /**
   * To clear the screen
   * 
   * @return None 
   */
  function clearScreen() {
    echo exec("clear") . "\n";
  }
  
  /**
   * To present a Menu
   * 
   * @param string $description [optional] The description of the Menu
   * 
   * @return None
   */
  function present( $description = false ) {
    if ( $description ) {
      echo chr(27)."[1m--- ".$description." ( ".date("l d F H:i:s")." ) ---".chr(27)."[0m"."\n";
    }
    else {
      echo chr(27)."[1m--- ( ".date("l d F H:i:s")." ) ---".chr(27)."[0m"."\n";
    }
  }
  
  /**
   * Show the Menu (its list of tasks)
   * 
   * @param object $menu The Menu to show
   * @param object $zero [optional] If true, we also show the Task with the ID 0, which is almost "Quit"
   * 
   * @return None
   */
  function showMenu( $menu, $zero = false ) {
    $this->present($menu->name);
    echo "\nSelect a task:\n\n";
    
    $menu->showTasks();
    
    if ($zero) {
      echo "-------------------------------------------------------\n";
      echo "[0] ".$menu->task_list[0]->description."\n";
    }
    
    // Waiting for input
    $task = recup("\nSelected task: ");
    
    $success = false;
    foreach ( $menu->task_list as $key => $oneTask ) {
      if ( "$key" === $task ) {
        if ( is_callable($oneTask->procedure) ) {
          $success = true;
          $this->clearScreen();
          $oneTask->presentTask();
          call_user_func($oneTask->procedure, $menu);
        }
        else if ( $oneTask->procedure === "quit" ) {
          $this->quit();
        }
        
        break;
      }
    }
    
    if (!$success) {
      $this->clearScreen();
      cecho("Incorrect input", "red");
      echo "\n";
    }
    
    echo "\n";
    $this->showMenu($menu, true);
  }
  
  /**
   * To quit the program
   * 
   * @return None 
   */
  function quit() {
    $this->clearScreen();
    exit();
  }
  
  /**
   * Show the Tasks for a Menu, except for the ID 0
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
  
  /**
   * Present a Task
   * 
   * @return None
   */
  function presentTask() {
    $line = "##";
    for ( $i = 0; $i < strlen($this->description); $i++ ) {
      $line .= "#";
    }
    $line .= "##";
    
    echo $line."\n";
    echo "# ".$this->description." #\n";
    echo $line."\n\n";
  }
  
  /**
   * Show the return choice if set
   * 
   * @param string $choice The string to input to return
   * 
   * @return None
   */
  function showReturnChoice( $choice ) {
    if ( $choice !== "" ) {
      echo "[$choice] Return to main menu\n\n";
    }
  }
}

?>