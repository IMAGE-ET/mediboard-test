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
require_once "baseBackup.php";
require_once dirname(__FILE__)."/classes/Procedure.class.php";

/**
 * To replace a database
 * 
 * @param string $srcLocation Remote location, ie user@host. If localhost, symlink instead of scp
 * @param string $srcDir      Remote directory, ie /var/backup
 * @param string $srcDB       Source database name, ie mediboard
 * @param string $tgtDir      Temporary target directory, ie /tmp
 * @param string $tgtDB       Target database name, ie target_mediboard
 * @param string $restart     [optional] To restart the Mysql server (Warning), ie for InnoDB
 * @param string $safeCopy    [optional] To make a safe copy of existing target database first
 * @param string $mysqlDir    [optional] Directory where databases are stored
 * @param string $port        [optional] SSH port of the target remote location
 * @param string $localCopy   To do a local copy
 * 
 * @return void
 */
function replaceBase($srcLocation, $srcDir, $srcDB, $tgtDir, $tgtDB, $restart, $safeCopy, $mysqlDir, $port, $localCopy) {
  $currentDir = dirname(__FILE__);
  $event = $currentDir . "/../tmp/svnevent.txt";

  announce_script("Mediboard replace base");
  
  $restart = false; 
  if ($restart === "y") {
    $restart = true;
  }
  
  if ($port === "") {
    $port = "22";
  }
  
  $safeCopy = false;
  if ($safeCopy === "y") {
    $safeCopy = true;
  }
  
  $localCopy = true;
  if ($localCopy === "n") {
    $localCopy = false;
  }
  
  if ($mysqlDir === "") {
    $mysqlDir = "/var/lib/mysql";
  }
  
  if ($restart) {
    echo "\n";
    cecho("Warning !!!!!!!!!!!! This will restart the MySQL server", "white", "bold", "red");
    echo "\n\n";
  }

  // MySQL Path
  $path = "/etc/init.d/mysql";
  $mysql_path = "/etc/init.d/mysqld";
  if (file_exists($path)) {
    $mysql_path = $path;
  }
  
  // Retrieve archive
  $archive = "archive.tar.gz";
  @unlink($tgtDir . "/" . $archive);
  if ($localCopy) {
    $scp = shell_exec(
      "scp ".$srcLocation.":".$srcDir."/".$srcDB."-db/".$srcDB."-latest.tar.gz ".$tgtDir."/".$archive
    );
    echo $scp . "\n";
    
    if (!(check_errs(
      file_exists($tgtDir."/".$archive),
      false, "Failed to retrieve remote archive", "Succesfully retrieved remote archive!"
    ))) {
      exit(0);
    }
  }
  else {
    unlink($tgtDir . "/" . $archive);
    $res = symlink($srcDir . "/" . $srcDB . "-db/" . $srcDB . "-latest.tar.gz", $tgtDir . "/" . $archive);
    
    if (!(check_errs($res, false, "Failed to symlink local archive", "Successfully symlinked local archive!"))) {
      exit(0);
    }
  }
  
  // Extract base
  chdir($tgtDir);

  exec("tar -xf " . $archive, $tar, $return_var);
  check_errs($return_var, true, "Failed to extract files", "Succesfully extracted files");
  
  // Stop MySQL
  if ($restart) {
    exec($mysql_path . " stop", $stop, $return_var);
    check_errs($return_var, true, "Failed to stop mysql", "Succesfully stopped mysql");
  }
  
  $dir_target = $mysqlDir . "/" . $tgtDB;

  if ($safeCopy) {
    // Copy database
    $DATETIME = date("Y_m_d\TH_i_s");
    $res = rename($dir_target, $dir_target . "_" . $DATETIME);
    check_errs($res, false, "Failed to move MySQL target directory", "Successfully moved MySQL target directory");
    
    $res = mkdir($dir_target);
    check_errs(
      $res, false, "Failed to create mysql target directory", "Succesfully created mysql target directory"
    );
    
    $res = chown($dir_target, "mysql");
    check_errs($res, false, "Failed to change owner", "Succesfully changed owner");
    $res = chgrp($dir_target, "mysql");
    check_errs($res, false, "Failed to change group", "Succesfully changed group");		
  }
  else {
    // Delete files in mediboard database
    $i = 0;
    $tab = array();
    
    $glob = glob($dir_target . "/*");
    
    if ($glob) {
      foreach ($glob as $one_file) {
        if (($one_file != ".") && ($one_file != "..")) {
          $tab[$i] = unlink($one_file);
          $i++;
        }			
      }
      
      $res = true;
    }
    else {
      $res = false;
    }

    for ($i = 0; $i < count($tab); $i++) {
      $res = $res && $tab[$i];
    }

    check_errs($res, false, "Failed to delete files", "Successfully deleted files");
  }
  
  // Move table files
  chdir($srcDB);

  $i = 0;
  $tab2 = array();
  $glob = glob("*");
  
  if ($glob) {
    foreach ($glob as $one_file) {
      if (($one_file != ".") && ($one_file != "..")) {
        $tab2[$i] = rename($one_file, $dir_target . "/" . $one_file);
        $i++;
      }			
    }
    
    $res = true;
  }
  else {
    $res = false;
  }

  for ($i = 0; $i < count($tab2); $i++) {
    $res = $res && $tab2[$i];
  }
  check_errs($res, false, "Failed to move files", "Successfully moved files");
  
  // Change owner and group
  chdir($dir_target);
  
  $i = 0;
  $tab3 = array();
  $tab4 = array();
  $glob = glob("*");
  
  if ($glob) {
    foreach ($glob as $one_file) {
      if (($one_file != ".") && ($one_file != "..")) {
        $tab3[$i] = chgrp($one_file, "mysql");
        $tab4[$i] = chown($one_file, "mysql");
        $i++;
      }			
    }
    
    $res = true;
  }
  else {
    $res = false;
  }

  for ($i = 0; $i < count($tab3); $i++) {
    $res = $res && $tab3[$i] && $tab4[$i];
  }
  
  check_errs($res, false, "Failed to change owner and group", "Succesfully changed owner and group");
  
  // Start MySQL
  if ($restart) {
    exec($mysql_path . " start", $start, $return_var);
    check_errs($return_var, true, "Failed to start mysql", "Succesfully started mysql");
  }
  
  // Cleanup temporary archive
  $res = rrmdir($tgtDir . "/" . $srcDB);
  $res2 = unlink($tgtDir . "/" . $archive);
  check_errs(
    ($res && $res2), false, "Failed to delete temporary archive", "Succesfully deleted temporary archive"
  );

  // Write into event file
  if (file_exists($event)) {
    $fic = fopen($event, "a");
  }
  else {
    $fic = fopen($event, "w");
  }

  fwrite($fic, "\n#".date('Y-m-d H:i:s'));
  fwrite($fic, "\nreplaceBase: <strong>".$srcDB."</strong> to <strong>".$tgtDB."</strong>");
  fclose($fic);
}

/**
 * The Procedure for the replacebase function
 * 
 * @param Menu $backMenu The Menu for return
 * 
 * @return void
 */
function replacebaseProcedure(Menu $backMenu) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_srcLocation  = $procedure->createQuestion("Source location (if localhost 'symlink' instead of 'scp'): ");
  $srcLocation     = $procedure->askQuestion($qt_srcLocation);
  
  if ( $srcLocation === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu($backMenu, true);
    exit();
  }
  
  $qt_srcDir    = $procedure->createQuestion("Source directory (ie /var/backup): ");
  $srcDir       = $procedure->askQuestion($qt_srcDir);
  
  $qt_srcDB     = $procedure->createQuestion("Source database (ie mediboard): ");
  $srcDB        = $procedure->askQuestion($qt_srcDB);
  
  $qt_tgtDir    = $procedure->createQuestion("Target directory (ie /tmp) [default /tmp]: ", "/tmp");
  $tgtDir       = $procedure->askQuestion($qt_tgtDir);
  
  $qt_tgtDB     = $procedure->createQuestion("Target database (ie target_mediboard): ");
  $tgtDB        = $procedure->askQuestion($qt_tgtDB);
  
  $qt_restart   = $procedure->createQuestion(
    "Restart MySQL Server (Warning) (ie for InnoDB) [y or n, default n]? ", "n"
  );
  $restart      = $procedure->askQuestion($qt_restart);
  
  $qt_safeCopy  = $procedure->createQuestion(
    "Make a safe copy of existing target database first [y or n, default n]? ", "n"
  );
  $safeCopy     = $procedure->askQuestion($qt_safeCopy);
  
  $qt_mySQLDir  = $procedure->createQuestion(
    "MySQL directory where databases are stored [default /var/lib/mysql]: ", "/var/lib/mysql"
  );
  $mySQLDir     = $procedure->askQuestion($qt_mySQLDir);
  
  $qt_port      = $procedure->createQuestion("SSH port [default 22]: ", 22 );
  $port         = $procedure->askQuestion($qt_port);
  
  $qt_localCopy = $procedure->createQuestion("Make a distant copy (scp) [y or n, default y]? ", "y");
  $localCopy    = $procedure->askQuestion($qt_localCopy);
  
  echo "\n";
  replaceBase($srcLocation, $srcDir, $srcDB, $tgtDir, $tgtDB, $restart, $safeCopy, $mySQLDir, $port, $localCopy);
}

/**
 * Function to use replacebase in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function replaceBaseCall( $command, $argv ) {
  if (count($argv) == 10) {
    $srcLocation  = $argv[0];
    $srcDir       = $argv[1];
    $srcDB        = $argv[2];
    $tgtDir       = $argv[3];
    $tgtDB        = $argv[4];
    $restart      = $argv[5];
    $safeCopy     = $argv[6];
    $mysqlDir     = $argv[7];
    $port         = $argv[8];
    $localCopy    = $argv[9];
    
    replaceBase($srcLocation, $srcDir, $srcDB, $tgtDir, $tgtDB, $restart, $safeCopy, $mysqlDir, $port, $localCopy);
    return 0;
  }
  else {
    echo "\nUsage : $command replacebase <source_location> <source_directory>".
    "<source_database> <target_directory> <target_database> [options below]\n
<source_location>   : remote location, ie user@host. if localhost, symlink instead of scp
<source_directory>  : remote directory, ie /var/backup
<source_database>   : source database name, ie mediboard
<target_directory>  : temporary target directory, ie /tmp
<target_database>   : target database name, ie target_mediboard\n
Options :
[<restart>]         : to restart the Mysql server (Warning), ie for InnoDB, default no
[<safe_copy>]       : to make a safe copy of existing target database first, default no
[<MySQL directory>] : directory where databases are stored, default /var/lib/mysql
[<port>]            : ssh port of the target remote location, default 22
[<local_copy>]      : to do a local copy, default scp\n\n";
    return 1;
  }
}
