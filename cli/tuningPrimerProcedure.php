<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

require_once "utils.php";
require_once dirname(__FILE__)."/classes/Procedure.class.php";

/**
 * The Procedure for the tuning-primer Shell script
 * 
 * @param Menu $backMenu The Menu for return
 * 
 * @return void
 */
function tuningPrimerProcedure(Menu $backMenu) {
  $procedure = new Procedure();
  
  echo "Select a mode:\n\n";
  echo "[1] All (perform all checks) [default]\n";
  echo "[2] Prompt (prompt for login credintials and socket and execution mode)\n";
  echo "[3] Memory (run checks for tunable options which effect memory usage)\n";
  echo "[4] Disk, file (run checks for options which effect i/o performance or file handle limits)\n";
  echo "[5] InnoDB (run InnoDB checks)\n";
  echo "[6] Misc (run checks for that don't categorise".
  " well Slow Queries, Binary logs, Used Connections and Worker Threads)\n";
  echo "[7] Banner (show banner info)\n";
  echo "-------------------------------------------------------------------------------\n";
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_mode = $procedure->createQuestion("\nSelected mode: ");
  $mode = $procedure->askQuestion($qt_mode);
  
  switch ($mode) {
    case "1":
      $mode = "all";
      break;
      
    case "2":
      $mode = "prompt";
      break;
      
    case "3":
      $mode = "memory";
      break;
      
    case "4":
      $mode = "file";
      break;
      
    case "5":
      $mode = "innodb";
      break;
      
    case "6":
      $mode = "misc";
      break;
      
    case "7":
      $mode = "banner";
      break;
      
    case "":
      $mode = "all";
      break;
      
    case $choice:
      $procedure->clearScreen();
      $procedure->showMenu($backMenu, true);
      
    default:
      $procedure->clearScreen();
      cecho("Incorrect input", "red");
      echo "\n";
      setupProcedure($backMenu);
  }
  
  echo "\n";
  echo shell_exec("sh ".dirname(__FILE__ )."/tuning-primer.sh ".$mode)."\n";
}

/**
 * Function to use tuningprimer in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function tuningPrimerCall( $command, $argv ) {
  if (count($argv) == 1) {
    $mode = $argv[0];
    
    echo shell_exec("sh ".dirname(__FILE__ )."/tuning-primer.sh ".$mode)."\n";
    
    return 0;
  }
  else {
    echo "\nUsage : $command tuningprimer [<mode>]\n
Available modes:
all         : perform all checks (default)
prompt      : prompt for login credintials and socket and execution mode
mem, memory : run checks for tunable options which effect memory usage
disk, file  : run checks for options which effect i/o performance or file handle limits
innodb      : run InnoDB checks /* to be improved */
misc        : ".
        "run checks for that don't categorise well Slow Queries, Binary logs, Used Connections and Worker Threads
banner      : show banner info\n\n";

    return 1;
  }
}
