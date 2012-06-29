<?php

/**  
 * @category cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

require_once( "Task.class.php" );

require_once( "logPing.php" );
require_once( "logUptime.php" );
require_once( "update.php" );
require_once( "setup.php" );
require_once( "rsyncupdate.php" );
require_once( "baseBackup.php" );
require_once( "replaceBase.php" );
require_once( "request.php" );
require_once( "rotateBinlogs.php" );

require_once( "sendFileFTPProcedure.php" );
require_once( "tuningPrimerProcedure.php" );

// Création tache principale
$main = new Task( "main", "Main" );

// Menu d'accueil de la tache principale
$mainMenu = $main->createMenu( "Main menu" );

// Début création des taches

// Création de la tache de sortie du menu
$quit = new Task( "quit", "Quit" );
$mainMenu->appendTask( $quit );

// Update Mediboard
$update = new Task( "updateProcedure", "Update Mediboard SVN and user-oriented logger" );
$mainMenu->appendTask( $update );

// Setup Mediboard
$setup = new Task( "setupProcedure", "Configure groups and mods for Mediboard directories" );
$mainMenu->appendTask( $setup );

// RsyncUpdate Mediboard
$rsyncupdate = new Task( "rsyncUpdateProcedure", "Update and rsync Mediboard SVN" );
$mainMenu->appendTask( $rsyncupdate );

// Request Mediboard
$request = new Task( "requestProcedure", "Launch Mediboard request" );
$mainMenu->appendTask( $request );

// Replace base Mediboard
$replacebase = new Task( "replaceBaseProcedure", "Replace Mediboard database" );
$mainMenu->appendTask( $replacebase );

// Base backup Mediboard
$basebackup = new Task( "baseBackupProcedure", "Backup database on a daily basis" );
$mainMenu->appendTask( $basebackup );

// Send FTP file Mediboard
$sendfileftp = new Task( "sendFileFTPProcedure", "Send a file by FTP" );
$mainMenu->appendTask( $sendfileftp );

// Log ping Mediboard
$logping = new Task( "logPingProcedure", "Log Ping for server load analysis" );
$mainMenu->appendTask( $logping );

// Log uptime Mediboard
$loguptime = new Task( "logUptimeProcedure", "Log Uptime for server load analysis" );
$mainMenu->appendTask( $loguptime );

// Tuning-primer Mediboard
$tuningprimer = new Task( "tuningPrimerProcedure", "Run MySQL performance tuning primer script" );
$mainMenu->appendTask( $tuningprimer );

// Rotate binlogs Mediboard
$rotatebinlogs = new Task( "rotateBinlogsProcedure", "Rotate binlogs" );
$mainMenu->appendTask( $rotatebinlogs );

// Fin création des taches du menu

// Check if PHP file is called with parameters
$command = array_shift( $argv );
$task = array_shift( $argv );
$task = strtolower( $task );

if ( $task != "" ) {
  if ( $task != "help" ) {
    foreach ( $mainMenu->getTaskList() as $oneTask ) {
      $task2 = substr( $oneTask->procedure, 0, strpos( $oneTask->procedure, "Procedure" ) );
      if ( strtolower( $task2 ) == $task )  {
        if ( is_callable( $task2."Call" ) ) {
          call_user_func( $task2."Call", $command, $argv );
          exit();
        }
        else {
          echo "\n";
          cecho( $task2."Call is not a callable function.", "red", "bold" );
          echo "\n\n";
          exit();
        }
      }
    }
  }
  else {
    echo "\nUsage : $command <task> [<params>]\n
<task>  : task to run\n";

    $task_list = $mainMenu->getTaskList();
    array_shift( $task_list );
    
    foreach ( $task_list as $oneTask ) {
      if ( $oneTask->procedure  )
      $task = strtolower( substr( $oneTask->procedure, 0, strpos( $oneTask->procedure, "Procedure" ) ) );
      echo "  $task : ".$oneTask->description."\n";
    }
    echo "Options :
  [<params>]  : params for <task>, see $command <task>\n\n";
    
    return 1;
  }
}

// Affichage du menu
$main->clearScreen();
$main->showMenu( $mainMenu, true );

?>