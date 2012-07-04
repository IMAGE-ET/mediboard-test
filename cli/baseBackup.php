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
 * Enable you to create a database backup
 * 
 * @param string $method        Hotcopy or Dump method
 * @param string $username      Access database
 * @param string $password      Authenticate user
 * @param string $hostname      Database server
 * @param string $port          MySQL port
 * @param string $database      Database to backup, ie mediboard
 * @param string $backupPath    Backup path, ie /var/backup
 * @param string $time          Time in days before removal of files
 * @param string $binary        To create mysql binary log index
 * @param string $loginUsername Username used to send a mail when diskfull is detected
 * @param string $loginPassword Password for the username used to send a mail when diskfull is detected
 * 
 * @return None
 */
function baseBackup($method, $username, $password, $hostname, $port, $database, $backupPath, $time, $binary,
    $loginUsername, $loginPassword
) {
  $currentDir = dirname(__FILE__);
  announce_script("Database daily backup");
    
  if ($hostname === "") {
    $hostname = "localhost";
  }
  
  if ($port === "") {
    $port = "3306";
  }
  
  if ($time === "") {
    $time = "7";
  }
  
  $binary = false;
  if ($binary === "y") {
    $binary = true;
  }
  
  if ($loginUsername === "") {
    $loginUsername = "";
    $loginPassword = "";
  }

  info_script("Backuping ".$database." database");
  
  // Make complete path //
  // Make shell path
  $SHELL_PATH = $currentDir;
  
  // Make backup path
  force_dir($backupPath);
  
  // Make database path
  $BASE_PATH = $backupPath."/".$database."-db";
  force_dir($BASE_PATH);
  chdir($BASE_PATH);
  
  // If no enough free disk space (1.5 * size of database), send mail if provided and quit
  $mysql_conf = shell_exec("find /etc -name my.cnf 2>/dev/null|head -n 1");
  $mysql_conf = trim($mysql_conf);
  
  $mysql_data_root = "";
  $lines = file($mysql_conf);
  
  foreach ( $lines as $line_num => $line ) {
    if ( preg_match("/^(datadir)/m", $line) ) {
      $datadirPOS = strpos($line, "=");
      $mysql_data_root = trim(substr($line, $datadirPOS + 1));
    }
  }
  
  $dir = opendir($mysql_data_root);
  check_errs($dir, false, "Unable to determine MySQL data root", "MySQL data root found!");
  closedir($dir);
  
  $mysql_data_base = $mysql_data_root."/".trim($database);

  $database_size = 0;
  $database_files = getFiles($mysql_data_base."/");
  
  if ( $database_files ) {
    foreach ( $database_files as $one_database_file ) {
      $database_size += filesize($one_database_file);
    }
  }
  
  // Expanded size (database + tar)
  $needed_size = $database_size * 3 / 2;
  $available_size = disk_free_space($backupPath);
  
  if ( $available_size < $needed_size ) {
    if ( $loginUsername != "" ) {
        info_script("Send a mail using ".$loginUsername." login");
        
        // Name of the instance of mediboard
        $instance = basename(dirname($currentDir));
        file_get_contents(
          "http://localhost/".$instance."/?login=".$loginUsername.":".$loginPassword.
          "&m=system&a=ajax_send_mail_diskfull"
        );
    }
  }
  
  check_errs(
    ($available_size < $needed_size),
    1, "Needed space ".formatSize($needed_size)." exceeds available space ".formatSize($available_size),
    "Enough available space!"
  );
  
  // Male MySQL method //
  
  // removes previous hotcopy/dump if something went wrong
  rrmdir($database);
  
  $DATETIME = date('Y-m-d\TH-i-s');
  
  switch ($method) {
    case "hotcopy":
      $result = $database."/";
        
      $mysqlhotcopy = shell_exec(
        "mysqlhotcopy -h " . $hostname . " -P " . $port . " -u ".$username.
        " -p ".$password." ".$database." ".$BASE_PATH
      );
      check_errs($mysqlhotcopy, null, "Failed to create MySQL hot copy", "MySQL hot copy done!");
      
      if ( $binary ) {
        $databasebinlog = $database . "-" . $DATETIME . ".binlog.position";
        $link = mysql_connect($hostname . ":" . $port, $username, $password);
        check_errs($link, false, "Could not connect : ".mysql_error(), "Connected!");
    
        if ( !($link) ) {
          return 0;
        }
      
        $query = "SHOW MASTER STATUS";
        $res = mysql_query($query);
        mysql_close($link);
        
        $row = 0;
        if ( $res ) {
          $row = mysql_fetch_object($res);
        }
        
        $a = 0;
        if ( $row ) {
          $file = fopen($backupPath."/binlog-".$DATETIME.".index", "w");
  
          if ($file) {
            fwrite(
              $file, "File            Position	Binlog_Do_DB	Binlog_Ignore_DB
  "
            );
            fwrite(
              $file, $row->File."	".$row->Position."	        ".$row->Binlog_Do_DB."	".$row->Binlog_Ignore_DB."\n"
            );
            fclose($file);
            $a = 1;
          }
        }
        
        check_errs($a, 0, "Failed to create MySQL Binary log index", "MySQL Binary log index done!");
      }
      
      break;
        
    case "dump":
      $result = $database . ".sql";
  
      $mysqldump = shell_exec(
        "mysqldump --opt -u ".$username." -p".$password." -h ".$hostname." -P ".$port." ".$database
      );
      check_errs($mysqldump, null, "Failed to create MySQL dump", "MySQL dump done!");
      
      $file = fopen($result, "w");
      $a = 0;
      
      if ( $file ) {
        fwrite($file, $mysqldump);
        fclose($file);
        $a = 1;
      }
      
      check_errs($a, 0, "Failed to save MySQL dump file", "MySQL dump file saved!");
  
      break;
    
    default:
      $result = $database . "/";
      echo "Choose hotcopy or dump method\n";
      
      return 0;
  }
  
  // Rotating files older than n days, all files if 0
  if ( !($time) ) {
    exec("find " . $BASE_PATH . " -name '" . $database . "*.tar.gz'", $find, $return_var);

    foreach ($find as $one_file) {
      unlink($one_file);
    }
    
    check_errs($return_var, true, "Failed to rotate files", "Files rotated");
  }
  else {
    $filter = "-ctime +" . $time;
    exec("find " . $BASE_PATH . " -name '" . $database . "*.tar.gz' " . $filter, $find, $return_var);
    
    foreach ($find as $one_file) {
      unlink($one_file);
    }
    
    check_errs($return_var, true, "Failed to rotate files", "Files rotated");
  }
  
  // Compress archive and remove file //
  
  // Make the tarball
  $tarball = $database . "-" . $DATETIME . ".tar.gz";
  exec("tar cfz " . $tarball . " " . $result, $tar, $return_var);
  check_errs($return_var, true, "Failed to create backup tarball", "Tarball packaged!");
  
  // Create a symlink
  @unlink($database . "-latest.tar.gz");
  $res = symlink($tarball, $database . "-latest.tar.gz");
  check_errs($res, false, "Failed to create symlink", "Symlink created!");
  
  // Remove temporay files
  $a = 0;
  if (is_dir($result)) {
    if (rrmdir($result)) {
      $a = 1;
    }
  }
  else {
    if (unlink($result)) {
      $a = 1;
    }
  }
  
  check_errs($a, false, "Failed to clean MySQL files", "MySQL files cleaning done!");	
}

/**
 * Recursively remove a directory
 * From http://fr2.php.net/manual/fr/function.rmdir.php
 * 
 * @param string $dir The directory to remove
 * 
 * @return bool
 */ 
function rrmdir($dir) {
  $a = 0;
  
  if (is_dir($dir)) {
    $objects = scandir($dir);
    
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") {
          rrmdir($dir."/".$object);
        }
        else {
          unlink($dir."/".$object);
        }
      }
      
      reset($objects);
      
      if (rmdir($dir)) {
        $a = 1;
      }
    }
  }
  
  return $a;
}

/**
 * Get all the files from a directory
 * From http://fr.php.net/manual/fr/function.opendir.php
 * 
 * @param string $directory The directory to search in
 * @param array  $exempt    [optional] The files or directories to skip
 * @param array  &$files    The returned files
 * 
 * @return array
 */
function getFiles($directory, $exempt = array('.', '..', '.ds_store', '.svn'), &$files = array()) {
  $handle = opendir($directory);
  
  while ( false !== ( $resource = readdir($handle) ) ) {
    if ( !in_array(strtolower($resource), $exempt) ) {
      if ( is_dir($directory.$resource.'/') ) {
        array_merge($files, self::getFiles($directory.$resource.'/', $exempt, $files));
      }
      else {
        $files[] = $directory.$resource;
      }
    }
  }
  
  closedir($handle);
  return $files;
}

/**
 * To format a filesize
 * From http://www.php.net/manual/fr/function.disk-total-space.php
 * 
 * @param int $size the size of the file
 * 
 * @return float
 */
function formatSize($size) {
  switch (true) {
    case ($size > 1099511627776):
      $size /= 1099511627776;
      $suffix = 'TB';
      
      break;
      
    case ($size > 1073741824):
      $size /= 1073741824;
      $suffix = 'GB';
      
      break;
      
    case ($size > 1048576):
      $size /= 1048576;
      $suffix = 'MB';
      
      break;
      
    case ($size > 1024):
      $size /= 1024;
      $suffix = 'KB';
      
      break;
      
    default:
      $suffix = 'B';
  }
  
  return round($size, 2).$suffix;
}

/**
 * The Procedure for the basebackup function
 * 
 * @param object $backMenu The Menu for return
 * 
 * @return None
 */
function baseBackupProcedure( $backMenu ) {
  $procedure = new Procedure();
  
  echo "Method:\n\n";
  echo "[1] Hotcopy\n";
  echo "[2] Dump\n";
  echo "--------------------\n";
  
  $choice = "0";
  $procedure->showReturnChoice($choice);
  
  $qt_method = $procedure->createQuestion("\nSelected method: " );
  $method = $procedure->askQuestion($qt_method);
  
  switch ( $method ) {
    case "1":
      $method = "hotcopy";
      break;
      
    case "2":
      $method = "dump";
      break;
      
    case $choice:
      $procedure->clearScreen();
      $procedure->showMenu($backMenu, true);
      break;
      
    default:
      $procedure->clearScreen();
      cecho("Incorrect input", "red");
      echo "\n";
      baseBackupProcedure($backMenu);
  }
  
  $qt_username = $procedure->createQuestion("Username (to access database): ");
  $username = $procedure->askQuestion($qt_username);
  
  $password = prompt_silent();
  
  $hostname = "";
  $port = "";
  
  $qt_DBBackup = $procedure->createQuestion("Database to backup (ie mediboard): ");
  $DBBackup = $procedure->askQuestion($qt_DBBackup);
  
  $qt_backupPath = $procedure->createQuestion("Backup path (ie /var/backup): ");
  $backupPath = $procedure->askQuestion($qt_backupPath);
  
  $qt_time = $procedure->createQuestion("Time (in days before removal of files) [default 7]: ", 7);
  $time = $procedure->askQuestion($qt_time);
  
  if ($method == "hotcopy") {
    $qt_binLog = $procedure->createQuestion("Create a binary log index [y or n, default n]? ", "n");
    $binLog = $procedure->askQuestion($qt_binLog);
  }
  else {
    $binLog = "";
  }
  
  $qt_mail = $procedure->createQuestion("Send a mail when diskfull is detected [y or n, default n]? ", "n");
  $mail = $procedure->askQuestion($qt_mail);
  
  if ($mail == "y") {
    $qt_usernameMail = $procedure->createQuestion("Username (to send a mail): ");
    $usernameMail = $procedure->askQuestion($qt_usernameMail);
    
    $passwordMail = prompt_silent();
  }
  else {
    $usernameMail = "";
    $passwordMail = "";
  }
  
  echo "\n";
  baseBackup(
    $method, $username, $password, $hostname, $port, $DBBackup,
    $backupPath, $time, $binLog, $usernameMail, $passwordMail
  );
}

/**
 * Function to use basebackup in one line
 * 
 * @param string $command The command input
 * @param array  $argv    The given parameters
 * 
 * @return bool
 */
function baseBackupCall( $command, $argv ) {
  if (count($argv) == 11) {
    $method         = $argv[0];
    $username       = $argv[1];
    $password       = $argv[2];
    $hostname       = $argv[3];
    $port           = $argv[4];
    $database       = $argv[5];
    $backupPath     = $argv[6];
    $time           = $argv[7];
    $binary         = $argv[8];
    $loginUsername  = $argv[9];
    $loginPassword  = $argv[10];
    
    baseBackup(
      $method, $username, $password, $hostname, $port, $database,
      $backupPath, $time, $binary, $loginUsername, $loginPassword
    );
    
    return 0;
  }
  else {
    echo "\nUsage : $command basebackup <method> <username> <password> <database> <backup_path> [options below]\n
<method>              : hotcopy or dump method
<username>            : access database
<password>            : authenticate user
<hostname>            : database server, default localhost
<port>                : MySQL port, default 3306
<database>            : database to backup, ie mediboard
<backup_path>         : backup path, ie /var/backup\n
Options :
[<time>]              : time in days before removal of files, default 7
[<binary_log_index>]  : to create mysql binary log index, default no
[<login_username>]    : username login to send a mail when diskfull is detected
[<login_password>]    : password login to send a mail when diskfull is detected\n\n";
    
    return 1;
  }
}
?>
