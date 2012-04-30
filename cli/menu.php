<?php

require_once("utils.php");
require_once("logPing.php");
require_once("logUptime.php");
require_once("update.php");
require_once("setup.php");
require_once("rsyncupdate.php");
require_once("baseBackup.php");
require_once("replaceBase.php");
require_once("request.php");
require_once("rotateBinlogs.php");

$currentDir = dirname(__FILE__);

// Check if PHP file is called with parameters
$command = array_shift($argv);
$task = array_shift($argv);
$task = strtolower($task);

switch($task) {
  
  case "update":
    
    if (count($argv) == 2) {
  
      $action = $argv[0];
      $revision = $argv[1];
      
      update($action, $revision);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <action> [<revision>]\n
<action>   : action to perform: info|real|noup
  info:  : shows the update log, no rsync
  real:   : performs the actual update and the rsync\n
Option:
  [<revision>] : revision number you want to update to, default HEAD\n\n";
      return 1;
    }
    break;
  
  case "setup":
    
    if (count($argv) == 3) {
  
      $mediboardDir = $argv[0];
      $subDir = $argv[1];
      $apacheGrp = $argv[2];
      
      setup($mediboardDir, $subDir, $apacheGrp);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <mediboard directory> [<sub directory>] [<apache group>]\n
<mediboard directory>  : mediboard directory you want to apply changes\n
Options :
  [<sub directory>]   : modules|style
  [<apache group>]    : name of the primary group for apache user\n\n";
      return 1;
    }
    break;
    
  case "rsyncupdate":
    
    if (count($argv) == 2) {
  
      $action = $argv[0];
      $revision = $argv[1];
      
      rsyncupdate($action, $revision);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <action> [<revision>]\n
<action>   : action to perform: info|real|noup
  info:  : shows the update log, no rsync
  real:   : performs the actual update and the rsync
  noup:   : no update, only rsync\n
Option:
  [<revision>] : revision number you want to update to, default HEAD\n\n";        
      return 1;
    }
    break;
    
  case "request":
    
    if (count($argv) == 7) {
  
      $rootURL = $argv[0];
      $username = $argv[1];
      $password = $argv[2];
      $params = $argv[3];
      $times = $argv[4];
      $delay = $argv[5];
      $file = $argv[6];
      
      request($rootURL, $username, $password, $params, $times, $delay, $file);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <rootURL> <username> <password> \"<params>\" [options below]\n
<rootURL>    : host to request (ie, https://localhost/mediboard)
<username>   : username requesting
<password>   : password of the user
\"<params>\" : parameters to send (ie, \"m=dPpatients&tab=vw_medecins\")\n
Options :
  [<times>] : how many repeats, default 1
  [<delay>] : delay (in seconds) between repeats, default 1
  [<file>]  : output file\n\n";
      return 1;
    }
    break;
    
  case "replacebase":
    
    if (count($argv) == 10) {
  
      $srcLocation = $argv[0];
      $srcDir      = $argv[1];
      $srcDB = $argv[2];
      $tgtDir = $argv[3];
      $tgtDB = $argv[4];
      $restart = $argv[5];
      $safeCopy = $argv[6];
      $mysqlDir = $argv[7];
      $port = $argv[8];
      $localCopy = $argv[9];
      
      replaceBase($srcLocation, $srcDir, $srcDB, $tgtDir, $tgtDB, $restart, $safeCopy, $mysqlDir, $port, $localCopy);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <source_location> <source_directory> <source_database> <target_directory> <target_database> [options below]\n
<source_location>  : remote location, ie user@host. if localhost, symlink instead of scp
<source_directory> : remote directory, ie /var/backup
<source_database>  : source database name, ie mediboard
<target_directory> : temporary target directory, ie /tmp
<target_database>  : target database name, ie target_mediboard\n
Options :
  [<restart>]         : to restart the Mysql server (Warning), ie for InnoDB, default no
  [<safe_copy>]       : to make a safe copy of existing target database first, default no
  [<MySQL directory>] : directory where databases are stored, default /var/lib/mysql
  [<port>]            : ssh port of the target remote location, default 22
  [<local_copy>]      : to do a local copy, default scp\n\n";
      return 1;
    }
    break;
    
  case "basebackup":
    
    if (count($argv) == 11) {
  
      $method = $argv[0];
      $username = $argv[1];
      $password = $argv[2];
      $hostname = $argv[3];
      $port = $argv[4];
      $database = $argv[5];
      $backupPath = $argv[6];
      $time = $argv[7];
      $binary = $argv[8];
      $loginUsername = $argv[9];
      $loginPassword = $argv[10];
      
      baseBackup($method, $username, $password, $hostname, $port, $database, $backupPath, $time, $binary, $loginUsername, $loginPassword);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <method> <username> <password> <database> <backup_path> [options below]\n
<method>       : hotcopy or dump method
<username>     : access database
<password>     : authenticate user
<hostname>     : database server, default localhost
<port>         : MySQL port, default 3306
<database>     : database to backup, ie mediboard
<backup_path>  : backup path, ie /var/backup\n
Options :
  [<time>]             : time in days before removal of files, default 7
  [<binary_log_index>] : to create mysql binary log index, default no
  [<login_username>]   : username login to send a mail when diskfull is detected
  [<login_password>]   : password login to send a mail when diskfull is detected\n\n";
      return 1;
    }
    break;
    
  case "sendfileftp":
    
    if (count($argv) == 7) {
  
      $hostname = $argv[0];
      $username = $argv[1];
      $password = $argv[2];
      $file = $argv[3];
      $port = $argv[4];
      $passiveMode = $argv[5];
      $ASCIIMode = $argv[6];
      
      $commandLine = "php " . $currentDir . "/sendFileFTP.php " . $hostname . " " . $username . " " . $password . " " . $file;

      if ($port != "") {
    
        $commandLine .= " -p " . $port;
      }
    
      if ($passiveMode == "y") {
    
        $commandLine .= " -m";
      }
    
      if ($ASCIIMode == "y") {
    
        $commandLine .= " -t";
      }
    
      echo shell_exec($commandLine) . "\n\n";
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <hostname> <username> <password> <file> options\n
<hostname> : host to connect
<username> : username requesting
<password> : password of the user
<file>     : file to send\n
Options :
  [<port>]       : port to connect, default 21
  [<passive_mode>] : switch to passive mode, default n
  [<ascii_mode>]   : switch to ascii mode, default n\n\n";
      return 1;
    }
    break;
    
  case "logping":
    
    if (count($argv) == 3) {
  
      $hostname = $argv[0];
      $fileLog = $argv[1];
      $dirLog = $argv[2];
      
      logPing($hostname, $fileLog, $dirLog);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task <host> [<output_filename>] [<directory>]\n
<host>  : target to the ping\n
Options :
  [<output_filename>]  : filename for the output, default <hostname>.log
  [<directory>]        : directory where to create <output_filename>, default /var/log/ping\n\n";
      return 1;
    }
    break;
    
  case "loguptime":
    
    if (count($argv) == 2) {
  
      $file = $argv[0];
      $hostname = $argv[1];
      
      logUptime($file, $hostname);
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task [<file>] [<hostname>]\n
<file>  : target for log, default /var/log/uptime.log\n
Options :
  [<hostname>]  : hostname for uptime, default localhost\n\n";
      return 1;
    }
    break;
    
  case "tuning-primer":
    
    if (count($argv) == 1) {
  
      $mode = $argv[0];
      
      echo shell_exec("sh " . $currentDir . "/tuning-primer.sh " . $mode) . "\n";
      return 0;
    }
    else {
      
      echo "\nUsage : $command $task [<mode>]\n
Available modes:
all         : perform all checks (default)
prompt      : prompt for login credintials and socket and execution mode
mem, memory : run checks for tunable options which effect memory usage
disk, file  : run checks for options which effect i/o performance or file handle limits
innodb      : run InnoDB checks /* to be improved */
misc        : run checks for that don't categorise well Slow Queries, Binary logs, Used Connections and Worker Threads
banner      : show banner info\n\n";
      return 1;
    }
    break;
    
  case "rotatebinlogs":
  	
    	if (count($argv) == 4) {
    	  $userAdminDB = $argv[0];
        $passAdminDB = $argv[1];
        $binLogsDir = $argv[2];
        $binLogIndexFilename = $argv[3];
        
        rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename);
        return 0;
    	}
      else {
        echo "Usage: $command $task <MySQL_username> <MySQL_password> <binlogs_directory> <binlog-index_filename>\n
<MySQL_username>  is the MySQL username allowed to connect, ie admin
<MySQL_password> is the password of the MySQL user
<binlogs_directory>  is the directory where binlogs are stored, ie /var/log/mysql
<binlog-index_filename> is the name of the binlog-index file, ie log-bin.index\n\n";
        return 1;
      }
  	break;
    
  case "help":
    
    echo "\nUsage : $command <task> [<params>]\n
<task>  : task to run
  update         : Update Mediboard SVN and user-oriented logger
  setup          : Configure groups and mods for Mediboard directories
  rsyncupdate    : Update and rsync Mediboard SVN
  request        : Launch Mediboard request
  replaceBase    : Replace Mediboard database
  baseBackup     : Backup database on a daily basis
  sednFileFTP    : Send a file by FTP
  logPing        : Log Ping for server load analysis
  logUptime      : Log Uptime for server load analysis
  tuning-primer  : Run MySQL performance tuning primer script\n
Options :
  [<params>]  : params for <task>, see $command <task>\n\n";
      return 1;
}

echo exec("clear") . "\n";
menu();

function menu() {
  echo chr(27)."[1m--- Main menu (".date("l d F H:i:s").") ---".chr(27)."[0m"."\n";
  echo "\nSelect a task:\n\n";

  echo "[1] Update Mediboard SVN and user-oriented logger\n";
  echo "[2] Configure groups and mods for Mediboard directories\n";
  echo "[3] Update and rsync Mediboard SVN\n";
  echo "[4] Launch Mediboard request\n";
  echo "[5] Replace Mediboard database\n";
  echo "[6] Backup database on a daily basis\n";
  echo "[7] Send a file by FTP\n";
  echo "[8] Log Ping for server load analysis\n";
  echo "[9] Log Uptime for server load analysis\n";
  echo "[10] Run MySQL performance tuning primer script\n";
  echo "[11] Rotate binlogs\n";
  echo "-------------------------------------------------------\n";
  echo "[0] Quit\n";
  
  // Waiting for input
  echo "\nSelected task: ";

  // Getting interactive input
  $task = trim(fgets(STDIN));

  // According to the task...
  switch ($task) {

    // Update Mediboard SVN
    case "1":
      echo exec("clear") . "\n";
      task1();
      break;

    // Configure groups and mods
    case "2":
      echo exec("clear") . "\n";
      task2();
      break;

    // Update and rsync Mediboard SVN
    case "3":
      echo exec("clear") . "\n";
      task3();
      break;

    // Launch Mediboard request
    case "4":
      echo exec("clear") . "\n";
      task4();
      break;

    // Replace Mediboard database
    case "5":
      echo exec("clear") . "\n";
      task5();
      break;
    // Database backup
    case "6":
      echo exec("clear") . "\n";
      task6();
      break;

    // Send a file by FTP
    case "7":
      echo exec("clear") . "\n";
      task7();
      break;
      
    // Log Ping for server load analysis
    case "8":
      echo exec("clear") . "\n";
      task8();
      break;
      
    // Log Uptime for server load analysis
    case "9":
      echo exec("clear") . "\n";
      task9();
      break;
      
    // Run MySQL performance tuning primer script
    case "10":
      echo exec("clear") . "\n";
      task10();
      break;
      
    // Rotate binlogs
    case "11":
    	echo exec("clear") . "\n";
      task11();
      break;

    // Exit program
    case "0":
      exit();
      break;

    // No action
    default:
      echo exec("clear") . "\n";
      echo "Incorrect input\n";
      menu();
  }
}

function task1() {

  echo "#################################################\n";
  echo "# Update Mediboard SVN and user-oriented logger #\n";
  echo "#################################################\n\n";

  echo "Action to perform:\n\n";
  echo "[1] Show the update log\n";
  echo "[2] Perform the actual update\n";
  echo "--------------------------------\n";
  echo "[0] Return to main menu\n";
  echo "\nSelected action: ";
  $action = trim(fgets(STDIN));

  switch ($action) {

    case "1":
      $action = "info";
      break;
      
    case "2":
      $action = "real";
      break;
      
    case "0":
      echo exec("clear") . "\n";
      menu();
      break;
      
    default:
      echo exec("clear") . "\n";
      echo "Incorrect input\n";
      task1();
  }

  echo "\nRevision number [default HEAD]: ";
  $revision = trim(fgets(STDIN));
  
  echo "\n";
  update($action, $revision);
  menu();
}

function task2() {

  echo "#######################################################\n";
  echo "# Configure groups and mods for Mediboard directories #\n";
  echo "#######################################################\n\n";

  echo "[0] Return to main menu\n\n";

  echo "Mediboard directory: ";
  $medDir = trim(fgets(STDIN));

  if ($medDir == "0") {

    echo exec("clear") . "\n";
    menu();
  }
  
  echo "\nSelect an optional sub directory [default none]:\n\n";
  echo "[1] modules\n";
  echo "[2] style\n";
  echo "--------------------------------------------------\n";
  echo "[0] No sub directory\n";
  echo "\nSelected sub directory: ";
  $subDir = trim(fgets(STDIN));

  switch ($subDir) {

    case "1":
      $subDir = "modules";
      break;
      
    case "2":
      $subDir = "style";
      break;    
  }

  echo "\nApache user's group [optional]: ";
  $apacheGrp = trim(fgets(STDIN));    

  echo "\n";
  setup($medDir, $subDir, $apacheGrp);
  menu();
}

function task3() {

  echo "##################################\n";
  echo "# Update and rsync Mediboard SVN #\n";
  echo "##################################\n\n";

  echo "Action to perform:\n\n";
  echo "[1] Show the update log\n";
  echo "[2] Perform the actual update\n";
  echo "[3] No update, only rsync\n";
  echo "--------------------------------\n";
  echo "[0] Return to main menu\n";
  echo "\nSelected action: ";
  $action = trim(fgets(STDIN));

  switch ($action) {

    case "1":
      $action = "info";
      break;
      
    case "2":
      $action = "real";
      break;
      
    case "3":
      $action = "noup";
      break;
      
    case "0":
      echo exec("clear") . "\n";
      menu();
      break;
      
    default:
      echo exec("clear") . "\n";
      echo "Incorrect input\n";
      task3();
  }

  echo "\nRevision number [default HEAD]: ";
  $revision = trim(fgets(STDIN));
  
  echo "\n";
  rsyncupdate($action, $revision);
  menu();
}

function task4() {

  echo "############################\n";
  echo "# Launch Mediboard request #\n";
  echo "############################\n\n";

  echo "[0] Return to main menu\n\n";

  echo "Root URL (ie https://localhost/mediboard): ";
  $rootURL = trim(fgets(STDIN));

  if ($rootURL == "0") {

    echo exec("clear") . "\n";
    menu();
  }

  echo "Username (ie cron): ";
  $username = trim(fgets(STDIN));

  $password = prompt_silent();

  echo "Params (ie m=dPpatients&tab=vw_medecins): ";
  $params = trim(fgets(STDIN));

  echo "Times (number of repetitions) [default 1]: ";
  $times = trim(fgets(STDIN));
  if ($times == "") {

    $times = 1;
  }

  echo "Delay (time between each repetition) [default 1]: ";
  $delay = trim(fgets(STDIN));
  if ($delay == "") {

    $delay = 1;
  }

  echo "File (file for the output, ie log.txt) [default no file]: ";
  $file = trim(fgets(STDIN));
  
  echo "\n";
  request($rootURL, $username, $password, $params, $times, $delay, $file);
  menu();
}

function task5() {

  echo "##############################\n";
  echo "# Replace Mediboard database #\n";
  echo "##############################\n\n";

  echo "[0] Return to main menu\n\n";

  echo "Source location (if localhost 'symlink' instead of 'scp'): ";
  $srcLocation = trim(fgets(STDIN));

  if ($srcLocation == "0") {

    echo exec("clear") . "\n";
    menu();
  }

  echo "Source directory (ie /var/backup): ";
  $srcDir = trim(fgets(STDIN));

  echo "Source database (ie mediboard): ";
  $srcDB = trim(fgets(STDIN));

  echo "Target directory (ie /tmp) [default /tmp]: ";
  $tgtDir = trim(fgets(STDIN));
  
  if ($tgtDir == "") {
    
    $tgtDir = "/tmp";
  }

  echo "Target database (ie target_mediboard): ";
  $tgtDB = trim(fgets(STDIN));

  echo "Restart MySQL Server (Warning) (ie for InnoDB) [y or n, default n]? ";
  $restart = trim(fgets(STDIN));

  echo "Make a safe copy of existing target database first [y or n, default n]? ";
  $safeCopy = trim(fgets(STDIN));

  echo "MySQL directory where databases are stored (ie /var/lib/mysql) [default /var/lib/mysql]: ";
  $mySQLDir = trim(fgets(STDIN));

  echo "SSH port [default 22]: ";
  $port = trim(fgets(STDIN));

  echo "Make a distant copy (scp) [y or n, default y]? ";
  $localCopy = trim(fgets(STDIN));

  echo "\n";
  replaceBase($srcLocation, $srcDir, $srcDB, $tgtDir, $tgtDB, $restart, $safeCopy, $mySQLDir, $port, $localCopy);
  menu();
}

function task6() {

  echo "####################################\n";
  echo "# Backup database on a daily basis #\n";
  echo "####################################\n\n";

  echo "Method:\n\n";
  echo "[1] Hotcopy\n";
  echo "[2] Dump\n";
  echo "--------------------\n";
  echo "[0] Return to main menu\n";
  echo "\nSelected method: ";
  $method = trim(fgets(STDIN));

  switch ($method) {

    case "1":
      $method = "hotcopy";
      break;
      
    case "2":
      $method = "dump";
      break;
      
    case "0":
      echo exec("clear") . "\n";
      menu();
      break;
      
    default:
      echo exec("clear") . "\n";
      echo "Incorrect input\n";
      task6();
  }

  echo "Username (to access database): ";
  $username = trim(fgets(STDIN));

  $password = prompt_silent();
  
  //echo "Hostname (remote database location) [default localhost]: ";
  //$hostname = trim(fgets(STDIN));
  
  //echo "Port (MySQL server) [default 3306]: ";
  //$port = trim(fgets(STDIN));

  $hostname = "";
  $port = "";

  echo "Database to backup (ie mediboard): ";
  $DBBackup = trim(fgets(STDIN));

  echo "Backup path (ie /var/backup): ";
  $backupPath = trim(fgets(STDIN));

  echo "Time (in days before removal of files) [default 7]: ";
  $time = trim(fgets(STDIN));

  if ($method == "hotcopy") {
    
    echo "Create a binary log index [y or n, default n]? ";
    $binLog = trim(fgets(STDIN));
  }
  else {
    
    $binLog = "";
  }

  echo "Send a mail when diskfull is detected [y or n, default n]? ";
  $mail = trim(fgets(STDIN));
  if ($mail == "y") {

    echo "Username (to send a mail): ";
    $usernameMail = trim(fgets(STDIN));

    $passwordMail = prompt_silent();
  }
  else {
    
    $usernameMail = "";
    $passwordMail = "";
  }

  echo "\n";
  baseBackup($method, $username, $password, $hostname, $port, $DBBackup, $backupPath, $time, $binLog, $usernameMail, $passwordMail);
  menu();
}

function task7() {

  echo "######################\n";
  echo "# Send a file by FTP #\n";
  echo "######################\n\n";

  echo "[0] Return to main menu\n\n";

  echo "Hostname: ";
  $hostname = trim(fgets(STDIN));

  if ($hostname == "0") {

    echo exec("clear") . "\n";
    menu();
  }

  echo "Username: ";
  $username = trim(fgets(STDIN));

  $password = prompt_silent();

  echo "File: ";
  $file = trim(fgets(STDIN));

  echo "Port [default 21]: ";
  $port = trim(fgets(STDIN));

  echo "Switch to passive mode [y or n, default n]? ";
  $passiveMode = trim(fgets(STDIN));

  echo "Switch to ASCII mode [y or n, default n]? ";
  $ASCIIMode = trim(fgets(STDIN));

  $commandLine = "php " . $GLOBALS['currentDir'] . "/sendFileFTP.php " . $hostname . " " . $username . " " . $password . " " . $file;

  if ($port != "") {

    $commandLine .= " -p " . $port;
  }

  if ($passiveMode == "y") {

    $commandLine .= " -m";
  }

  if ($ASCIIMode == "y") {

    $commandLine .= " -t";
  }

  echo shell_exec($commandLine) . "\n\n";
  menu();
}

function task8() {
  
  echo "###################################\n";
  echo "Log Ping for server load analysis #\n";
  echo "###################################\n\n";
  
  echo "[0] Return to main menu\n\n";

  echo "Hostname: ";
  $hostname = trim(fgets(STDIN));

  if ($hostname == "0") {

    echo exec("clear") . "\n";
    menu();
  }
  
  if ($hostname === "") {
    
    $hostname == "localhost";
  }
  
  echo "File directory [default /var/log/ping]: ";
  $dir = trim(fgets(STDIN));

  if ($dir === "") {
    
    $dir = "/var/log/ping";
  }
  
  echo "Output file [default " . $hostname . ".log]: ";
  $file = trim(fgets(STDIN));
  
  if ($file === "") {
    
    $file = $hostname . ".log";
  }

  echo "\n";
  logPing($hostname, $file, $dir);
  menu();
}

function task9() {
  
  echo "###################################\n";
  echo "Log Uptime for server load analysis #\n";
  echo "###################################\n\n";
  
  echo "[0] Return to main menu\n\n";

  echo "Hostname [default localhost]: ";
  $hostname = trim(fgets(STDIN));

  if ($hostname == "0") {

    echo exec("clear") . "\n";
    menu();
  }

  echo "File (target for log, ie /var/log/uptime.log) [default /var/log/uptime.log]: ";
  $file = trim(fgets(STDIN));

  if ($file === "") {
    
    $file = "/var/log/uptime.log";
  }

  echo "\n";
  logUptime($file, $hostname);
  menu();
}

function task10() {
  
  echo "############################################\n";
  echo "Run MySQL performance tuning primer script #\n";
  echo "############################################\n\n";

  echo "Select a mode:\n\n";
  echo "[1] All (perform all checks [default]\n";
  echo "[2] Prompt (prompt for login credintials and socket and execution mode)\n";
  echo "[3] Memory (run checks for tunable options which effect memory usage)\n";
  echo "[4] Disk, file (run checks for options which effect i/o performance or file handle limits)\n";
  echo "[5] InnoDB (run InnoDB checks)\n";
  echo "[6] Misc (run checks for that don't categorise well Slow Queries, Binary logs, Used Connections and Worker Threads)\n";
  echo "[7] Banner (show banner info)\n";
  echo "-------------------------------------------------------------------------------\n";
  echo "[0] Return to main menu\n";
  echo "\nSelected mode: ";
  $mode = trim(fgets(STDIN));

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
      
    case "0":
      echo exec("clear") . "\n";
      menu();
      break;
      
    default:
      echo exec("clear") . "\n";
      echo "Incorrect input\n";
      task10();
  }

  echo shell_exec("sh " . $GLOBALS['currentDir'] . "/../shell/tuning-primer.sh " . $mode) . "\n";
    menu();
}

function task11() {
  echo "##################\n";
  echo "# Rotate binlogs #\n";
  echo "##################\n\n";
  
  echo "[0] Return to main menu\n\n";

  echo "MySQL username: ";
  $userAdminDB = trim(fgets(STDIN));
  
  if ($userAdminDB === "0") {
    echo exec("clear") . "\n";
    menu();
  }
  
  $passAdminDB = prompt_silent("MySQL user password: ");
  
  echo "BinLogs directory [default /var/log/mysql]: ";
  $binLogsDir = trim(fgets(STDIN));
  
  if ($binLogsDir === "") {
    $binLogsDir = "/var/log/mysql";
  }
  
  echo "BinLog index filename [default log-bin.index]: ";
  $binLogIndexFilename = trim(fgets(STDIN));
  
  if ($binLogIndexFilename === "") {
    $binLogIndexFilename = "log-bin.index";
  }
  
  echo "\n";
  rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename);
  menu();
  
}
  
  force_dir($backupDir);

// In order to have a password prompt that works on many OS (works on Unix, Windows XP and Windows 2003 Server)
// Source : http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
function prompt_silent($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
          $vbscript, 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", "password here"))');
        $command = "cscript //nologo " . escapeshellarg($vbscript);
        $password = rtrim(shell_exec($command));
        unlink($vbscript);
        return $password;
    } else {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return;
        }
        $command = "/usr/bin/env bash -c 'read -s -p \"" . addslashes($prompt) . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        echo "\n";
        return $password;
    }
}

?>
