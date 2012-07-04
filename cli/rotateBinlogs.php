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
require_once "Procedure.class.php";

/**
 * Rotate MySQL Binlogs
 * 
 * @param string $userAdminDB         MySQL username allowed to connect, ie admin
 * @param string $passAdminDB         Password of the MySQL user
 * @param string $binLogsDir          Directory where binlogs are stored, ie /var/log/mysq
 * @param string $binLogIndexFilename Name of the binlog-index file, ie log-bin.index
 * @param string $backup              Backup directory, ie /mbbackup/binlogs
 * 
 * @return None
 */
function rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename, $backup) {
  $currentDir = dirname(__FILE__);
  
  announce_script("Rotate binlogs");
  
  if ($userAdminDB === "") {
    $userAdminDB = "mbadmin";
  }
  
  if ($binLogsDir === "") {
    $binLogsDir = "/var/log/mysql";
  }
  
  if ($binLogIndexFilename === "") {
    $binLogIndexFilename = "log-bin.index";
  }
  
  if ($backup === "") {
    $backup = "/mbbackup/binlogs";
  }
  
  // Backup destination dir
  force_dir($backup);
  
  // Flush logs to start a new one
  if ($passAdminDB === "") {
    echo shell_exec("mysqladmin -u ".$userAdminDB." flush-logs");
  }
  else {
    echo shell_exec("mysqladmin -u ".$userAdminDB." -p".$passAdminDB." flush-logs");
  }
  
  // Move all logs but latest to backup
  $binLogFiles = file($binLogsDir."/".$binLogIndexFilename);
  check_errs(
    $binLogFiles,
    false,
    "Impossible d'ouvrir le fichier ".$binLogsDir."/".$binLogIndexFilename,
    "Fichier ".$binLogsDir."/".$binLogIndexFilename." ouvert !"
  );
  
  $lastBinLogFile = trim(end($binLogFiles));
  
  $binPrefixPOS = strpos(basename($lastBinLogFile), ".");
  $binPrefix = substr(basename($lastBinLogFile), 0, $binPrefixPOS + 1);
  
  foreach (glob(dirname($lastBinLogFile)."/".$binPrefix."*") as $oneBinLogFile) {
    if ($oneBinLogFile != $lastBinLogFile) {
      rename($oneBinLogFile, $backup."/".basename($oneBinLogFile));
    }
  }
  
  // Move binlog indexes to binlog backup
  copy($binLogsDir."/".$binLogIndexFilename, $backup."/".$binLogIndexFilename);
  
  // Archive the binlogs
  exec(
    "tar -cjf ".$backup."/binlogs_".date('Y-m-d\TH:i:s').".tar.bz2 -C ".$backup." ".$binPrefix."*",
    $result,
    $returnVar
  );
  
  // Rotate binlogs
  foreach (glob($backup."/".$binPrefix."*") as $aBinLogFile) {
    unlink($aBinLogFile);
  }
  
  // Rotate binlogs and indeces for a week
  shell_exec("find $backup -name \"*bin.*\" -mtime +7 -exec rm -f {} \;");
  shell_exec("find $backup -name \"binlog-*.index\" -mtime +7 -exec rm -f {} \;");
  shell_exec("find $backup -name \"binlogs_*.tar.bz2\" -mtime +7 -exec rm -f {} \;");
}

/**
 * The Procedure for the rotatebinlogs function
 * 
 * @param object $backMenu The Menu for return
 * 
 * @return None
 */
function rotateBinlogsProcedure($backMenu) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_userAdminDB  = $procedure->createQuestion("MySQL username: ");
  $userAdminDB     = $procedure->askQuestion($qt_userAdminDB);
  
  if ( $userAdminDB === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu($backMenu, true);
    exit();
  }
  
  $passAdminDB = prompt_silent("MySQL user password: ");
  
  $qt_binLogsDir = $procedure->createQuestion("BinLogs directory [default /var/log/mysql]: ", "/var/log/mysql");
  $binLogsDir = $procedure->askQuestion($qt_binLogsDir);
  
  $qt_binLogIndexFilename = $procedure->createQuestion(
    "BinLog index filename [default log-bin.index]: ", "log-bin.index"
  );
  $binLogIndexFilename    = $procedure->askQuestion($qt_binLogIndexFilename);
  
  $qt_backupDir = $procedure->createQuestion(
    "Backup directory [default /mbbackup/binlogs]: ", "/mbbackup/binlogs"
  );
  $backupDir = $procedure->askQuestion($qt_backupDir);
  
  echo "\n";
  rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename, $backupDir);
}

/**
 * Function to use rotatebinlogs in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function rotateBinlogsCall( $command, $argv ) {
  if (count($argv) == 5) {
    $userAdminDB          = $argv[0];
    $passAdminDB          = $argv[1];
    $binLogsDir           = $argv[2];
    $binLogIndexFilename  = $argv[3];
    $backupDir            = $argv[4];
    
    rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename, $backupDir);
    return 0;
  }
  else {
    echo "Usage: $command rotatebinlogs <MySQL_username>".
    " <MySQL_password> <binlogs_directory> <binlog-index_filename> <backup>\n
<MySQL_username>  is the MySQL username allowed to connect, ie admin
<MySQL_password> is the password of the MySQL user
<binlogs_directory>  is the directory where binlogs are stored, ie /var/log/mysql
<binlog-index_filename> is the name of the binlog-index file, ie log-bin.index
<backup> is the backup directory, ie /mbbackup/binlogs\n\n";
    return 1;
  }
}
?>
