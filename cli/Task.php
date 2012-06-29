<?php

/**  
 * @category cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

require_once( "utils.php" );
require_once( "Question.php" );
require_once( "Menu.php" );

class Task {
  
  public $procedure   = null;
  public $description = null;
  
  function Task( $procedure, $description ) {
    $this->procedure      = $procedure;
    $this->description    = $description;
  }
  
  function createMenu( $name ) {
    return new Menu( $name );
  }
  
  function clearScreen() {
    echo exec("clear") . "\n";
  }
  
  // Presentation
  function present( $description = false ) {
    if ( $description ) {
      echo chr( 27 )."[1m--- ".$description." ( ".date( "l d F H:i:s" )." ) ---".chr( 27 )."[0m"."\n";
    }
    else {
      echo chr( 27 )."[1m--- ( ".date( "l d F H:i:s" )." ) ---".chr( 27 )."[0m"."\n";
    }
  }
  
  // Affiche interface d'accueil du menu
  function showMenu( $menu, $zero = false ) {
    $this->present( $menu->name );
    echo "\nSelect a task:\n\n";
    
    $menu->showTasks();
    
    if ($zero) {
      echo "-------------------------------------------------------\n";
      echo "[0] ".$menu->task_list[0]->description."\n";
    }
    
    // Waiting for input
    $task = recup( "\nSelected task: ");
    
    $success = false;
    foreach ( $menu->task_list as $key => $oneTask ) {
      if ( "$key" === $task ) {
        if ( is_callable( $oneTask->procedure ) ) {
          $success = true;
          $this->clearScreen();
          $oneTask->presentTask();
          call_user_func( $oneTask->procedure, $menu );
        }
        else if ( $oneTask->procedure === "quit" ) {
          $this->quit();
        }
        
        break;
      }
    }
    
    if (!$success) {
      $this->clearScreen();
      cecho( "Incorrect input", "red" );
      echo "\n";
    }
    
    echo "\n";
    $this->showMenu( $menu, true );
  }
  
  function quit() {
    $this->clearScreen();
    exit();
  }
  
  function showTasks() {
    foreach ( $this->getTaskList() as $nb => $oneTask ) {
      if ($nb !== 0) {
        echo "[".$nb."] ".$oneTask->description."\n";
      }
    }
  }
  
  // Presentation de la tache
  function presentTask() {
    $line = "##";
    for ( $i = 0; $i < strlen( $this->description ); $i++ ) {
      $line .= "#";
    }
    $line .= "##";
    
    echo $line."\n";
    echo "# ".$this->description." #\n";
    echo $line."\n\n";
  }
  
  function showReturnChoice( $choice ) {
    if ( $choice !== "" ) {
      echo "[$choice] Return to main menu\n\n";
    }
  }
}

?>