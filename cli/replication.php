<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

global $file;
echo exec("clear");
menu();

/**
 * Main menu of replication
 * 
 * @return None
 */
function menu() {
  global $file;
  
  // Check if a configuration file is alreay defined
  if (is_readable("/tmp/replicationConfigurationFile")) {
    $file = file("/tmp/replicationConfigurationFile");
    $file = $file[0];
  }
  
  echo chr(27)."[1m--- Main menu (".date("l d F H:i:s").") ---".chr(27)."[0m"."\n";
  
  if (!(is_null($file))) {
    echo "\nCurrent configuration file: ".$file."\n";
  }
  
  echo "\nSelect a task:\n\n";
  
  echo "[1] Set database name\n";
  echo "[2] Create or complete a configuration server file\n";
  echo "[3] Generate configuration\n";
  echo "[4] Run SQL queries\n";
  
  if (!(is_null($file))) {
    echo "[5] Change configuration file\n";
    echo "[6] Remove configuration file\n";
  }
  
  echo "---------------------------------------------------------\n";
  echo "[0] Quit\n";
  
  // Waiting for input
  echo "\nSelected task: ";
  
  // Getting interactive input
  $task = trim(fgets(STDIN));
  
  // According to the task...
  switch ($task) {
    case "1":
      echo exec("clear");
      
      // If there is no configuration file selected, we propose
      if (is_null($file)) {
        $file = recup("Choose a configuration file [default configServer.xml]: ", "configServer.xml");
        // Write the configuration filename
        file_put_contents("/tmp/replicationConfigurationFile", $file);
      }
      // To remove meta-characters
      else {
        $file = trim($file);
      }
      
      // Check the file
      check_file($file);
      
      // Create a new DOMDocument object
      $dom = new DOMDocument();
      
      // Load the XML file
      check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
      
      // Get the root node
      $root = $dom->documentElement;
      
      $dbname = recup("Database name [default mediboard] : ", "mediboard");
      $root->setAttribute("db", $dbname);
      
      $dom->save($file);
      
      echo exec("clear");
      menu();
      break;
      
    // Configure or create a configuration file
    case "2":
      echo exec("clear");
      
      // If there is no configuration file selected, we propose
      if (is_null($file)) {
        $file = recup("Choose a configuration file [default configServer.xml]: ", "configServer.xml");
      }
      // To remove meta-characters
      else {
        $file = trim($file);
      }
      
      // Call of the fonction
      configServer($file);
      echo "\n\n";
      menu();
      break;
      
    // Choose a configuration method (my.cnf or simple TXT file), then generate configuration file
    case "3":
      echo exec("clear");
      
      if (!(is_null($file))) {
        $file = trim($file);
        // Get the two servers name
        $servers = getServersName($file);
      }
      else {
        $file = recup("Choose a configuration file [default configServer.xml]: ", "configServer.xml");
        $servers = getServersName($file);
      }
      
      // We choose the server to configure
      if (!(is_null($servers)) && count($servers) == 2) {
        echo "\n";
        echo "[1] First server: ".$servers['firstServer']."\n";
        echo "[2] Second server: ".$servers['secondServer']."\n";
        echo "--------------------------------------------\n";
        echo "[0] Return to menu\n";
        $server = recup("\nFor which server ? [default 1]: ", "1");
      }
      else {
        echo exec("clear");
        cecho("There are no two servers in your file!", "red");
        echo "\n\n";
        menu();
      }
      
      switch ($server) {
        case "1":
          chooseConfMethod($file, $servers['firstServer']);
          echo "\n\n";
          menu();
          break;
          
        case "2":
          chooseConfMethod($file, $servers['secondServer']);
          echo "\n\n";
          menu();
          break;
          
        case "0":
          echo exec("clear");
          menu();
      }
      break;
      
    // Run SQL queries
    case "4":
      echo exec("clear");
      if (!(is_null($file))) {
        $file = trim($file);
        $servers = getServersName($file);
      }
      else {
        $file = recup("Choose a configuration file [default configServer.xml]: ", "configServer.xml");
        $servers = getServersName($file);
      }
      
      if (!(is_null($servers)) && count($servers) == 2) {
        echo "\n";
        echo "[1] First server: ".$servers['firstServer']."\n";
        echo "[2] Second server: ".$servers['secondServer']."\n";
        echo "--------------------------------------------\n";
        echo "[0] Return to menu\n";
        $server = recup("\nFor which server ? [default 1]: ", "1");
      }
      else {
        echo exec("clear");
        cecho("There are no two servers in your file!", "red");
        echo "\n\n";
        menu();
      }  
          
      switch ($server) {
        case "1":
          runSQLQueries($file, $servers['firstServer']);
          echo "\n\n";
          menu();
          break;
          
        case "2":
          runSQLQueries($file, $servers['secondServer']);
          echo "\n\n";
          menu();
          break;
          
        case "0":
          echo exec("clear");
          menu();
      }
      break;
      
    case "5":
      unlink("/tmp/replicationConfigurationFile");
      $file = null;
      echo exec("clear");
      menu();
      break;
      
    case "6":
      $fileToRemove = file("/tmp/replicationConfigurationFile");
      unlink($fileToRemove[0]);
      unlink("/tmp/replicationConfigurationFile");
      $file = null;
      echo exec("clear");
      menu();
      break;
      
    // Exit program
    case "0":
      echo exec("clear");
      exit();
      break;
      
    // No action
    default:
      echo exec("clear");
      cecho("Incorrect input.", "red");
      echo "\n\n";
      menu();
  }
}

/**
 * Enable you to configure a server and to write in a XML file
 * 
 * @param string $file Used configuration file
 * 
 * @return None 
 */
function configServer($file) {
  echo exec("clear");
  
  // Check the file
  check_file($file);
  
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
  
  // Write the configuration filename
  file_put_contents("/tmp/replicationConfigurationFile", $file);
  
  // Get the root node
  $root = $dom->documentElement;
  
  echo "\n";
  // Get the list of servers already in file
  $serverList = $dom->getElementsByTagName('serveur');
  
  switch ($serverList->length) {
    // If no one, nothing
    case 0:
      break;
      
    // If one, warning and show its name
    case 1:
      $serverNameInFile = $serverList->item(0)->getAttribute('nom');
      echo "There is already one server named ".$serverNameInFile." configurated in this file.\n";
      $answer = recup("Do you want to continue (y or n) [default y] ? ", "y");
      
      if ($answer == "n") {
        echo exec("clear");
        menu();
      }
      break;
      
    // If more than one, quit
    default:
      echo exec("clear");
      cecho("There is already two servers in this file.", "red");
      echo "\n\n";
      menu();
  }
  
  // Get the parameters for the server
  echo "\n";
  announce_script("Server configuration start");
  echo "\n";
  
  do {
    $a = true;
    $serverName = recup("Give a name to the server (it's for the XML file): ");
    
    /* Check for the server name redondancy */
    // Get the server from the XML file
    if ($serverList->length == 1) {
      foreach ($serverList as $aServer) {
        if ($aServer->getAttribute('nom') == $serverName) {
          cecho("This server already exists!", "red");
          echo "\n\n";
          $a = false;
        }
      }
    }
  } while (!($a));
  /* End check */
  
  $firstServer = recup("Is it the \"first\" server (odd primary keys...) (y or n) [default y] ? ", "y");
  
  // Because we will compare with XML
  if ($firstServer != "n") {
    $firstServer = "y";
  }
  
  // If there is already a server in the XML file
  if ($serverList->length == 1) {
    foreach ($serverList as $aServer) {
      if ($aServer->getAttribute('first') == $firstServer) {
        if ($firstServer == "y") {
          cecho(
            "This server cannot be first because ".$aServer->getAttribute('nom')." is already the first!", "red"
          );
          echo "\n\n";
          cecho("This server will be configured to be the second.", "red");
        }
        else {
          cecho(
            "This server cannot be second because ".$aServer->getAttribute('nom')." is already the second!", "red"
          );
          echo "\n\n";
          cecho("This server will be configured to be the first.", "red");
        }
      }
    }
  }
  
  $hostname = recup("Server IP: ");
  $localhost = recup("Is it the localhost address (y or n) [default n] ? ", "n");
  
  // Because we will compare with XML
  if ($localhost != "y") {
    $localhost = "n";
  }
  
  $DBPort = recup("Database port [default 3306]: ", 3306);
  $DBUser = recup("Database user: ");
  $DBPassword = prompt_silent("Database user password: ");
  $slaveUser = recup("Replication slave user: ");
  $slavePassword = prompt_silent("Replication slave password: ");
  $logBinFile = recup("Log-bin file [default /var/log/mysql/bin.log]: ", "/var/log/mysql/bin.log");
  $logBinIndex = recup("Log-bin index [default /var/log/mysql/log-bin.index]: ", "/var/log/mysql/log-bin.index");
  $logError = recup(
    "Log-error [default /var/log/mysql/error.log]: ", "/var/log/mysql/error.log"
  );
  $relayLogFile = recup("Relay-log file [default /var/log/mysql/relay.log]: ", "/var/log/mysql/relay.log");
  $relayLogInfoFile = recup(
    "Relay-log info file [default /var/log/mysql/relay-log.info]: ", "/var/log/mysql/relay-log.info"
  );
  $relayLogIndex = recup(
    "Relay-log index [default /var/log/mysql/relay-log.index]: ", "/var/log/mysql/relay-log.index"
  );
  echo "\n";
  info_script("Server configuration end");
  echo "\n";
  
  // Create DOMElement and set its attributes
  $newServer = $dom->createElement('serveur');
  $newServer->setAttribute('nom', $serverName);
  $newServer->setAttribute('first', $firstServer);
  $newServer->setAttribute('ip', $hostname);
  $newServer->setAttribute('localhost', $localhost);
  $newServer->setAttribute('dbport', $DBPort);
  $newServer->setAttribute('dbusername', $DBUser);
  $newServer->setAttribute('dbpassword', $DBPassword);
  
  $newSlave = $dom->createElement('esclave');
  $newSlave->setAttribute('username', $slaveUser);
  $newSlave->setAttribute('password', $slavePassword);
  
  $newLogBin = $dom->createElement('logbin');
  $newLogBin->setAttribute('logbinfile', $logBinFile);
  $newLogBin->setAttribute('logbinindex', $logBinIndex);
  $newLogBin->setAttribute('logerror', $logError);
  
  $newRelayLog = $dom->createElement('relaylog');
  $newRelayLog->setAttribute('relaylogfile', $relayLogFile);
  $newRelayLog->setAttribute('relayloginfofile', $relayLogInfoFile);
  $newRelayLog->setAttribute('relaylogindex', $relayLogIndex);
  
  // Put the nodes into the server node
  $newServer->appendChild($newSlave);
  $newServer->appendChild($newLogBin);
  $newServer->appendChild($newRelayLog);
  
  // Put the server node into the root node
  $root->appendChild($newServer);
  
  // Write the XML file
  check_errs($dom->save($file), false, "Unable to save the ".$file." file.", "File ".$file." saved!");
}

/**
  * Determine if we will modify my.cnf file or just create a TXT file
  * 
  * @param string $file Used configuration file
  * @param string $name Configuration method chosen
  * 
  * @return array
  */
function chooseConfMethod($file, $name) {
  echo exec("clear");
  
  // Check the file
  check_file($file);
  
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
  
  // Get the root node
  $root = $dom->documentElement;
  
  // Get the server from the XML file
  $myServer = getServerByName($name, $file);
  
  // Get the attributes
  $hostname = $myServer->getAttribute('ip');
  $localhost = $myServer->getAttribute('localhost');
  
  $myAnswerConf = recup("Use the my.cnf file (y or n) [default y] ? ", "y");
  
  if ($myAnswerConf != "n") {
    $myConf = recup("Path to my.cnf [default /etc/mysql/my.cnf]: ", "/etc/mysql/my.cnf");
    
    echo "\n";
    
    if ($localhost == "y") {
      check_errs(
        copy($myConf, $myConf.".".$hostname.".old"),
        false,
        "Unable to copy ".$myConf.".",
        $myConf." saved to ".$myConf.".".$hostname.".old"
      );
      
      // Selected method
      $method['method'] = "mysqld";
      $method['filename'] = $myConf;
      
    }
    else {
      $sshUser = recup("Username allowed to connect to ".$hostname.": ");
      exec("scp ".$sshUser."@".$hostname.":".$myConf." ".$myConf.".".$hostname.".old", $result, $returnVar);
      check_errs($returnVar, true, "Unable to SCP", "SCP copy worked properly!");
      
      // Copy of the .old file to the .new file in order to work
      check_errs(
        copy($myConf.".".$hostname.".old", $myConf.".".$hostname.".new"),
        false,
        "Unable to create new file ".$myConf.".".$hostname.".new",
        "New file ".$myConf.".".$hostname.".new successfully created!"
      );
      
      // Selected method
      $method['method'] = "mysqld";
      $method['filename'] = $myConf;
    }
  }
  else {
    $file1 = recup("Filename for the server configuration output [default ".$hostname.".txt]: ", $hostname.".txt");
    
    // Selected method
    $method['method'] = "file";
    $method['filename'] = $hostname.".txt";
  }
  
  // Generate the conf
  return generateConf($file, $method, $name);
}

/**
 * Enable you to generate a configuration file, my.cnf of just a TXT file
 * 
 * @param string $file   Used configuration file
 * @param string $method Configuration method chosen
 * @param string $name   Server name
 * 
 * @return array
 */
function generateConf($file, $method, $name) {
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
  
  // Get the root node
  $root = $dom->documentElement;
  
  echo "\n";
  // Get the list of servers already in file
  $serverList = $dom->getElementsByTagName('serveur');
  
  switch ($serverList->length) {
    // If two, we can continue
    case 2:
      // Get the server
      $myServer = getServerByName($name, $file);
      
      // Start of the configuration
      $commonConf = "# A inserer dans [mysqld] :

####### Debut replication circulaire #######

# Pas de blocage des binlogs pendant requetes
skip-external-locking

# Taille max. des binlogs
max_binlog_size         = 100M

# On ne replique pas a nouveau les donnees provenant de l'autre serveur
replicate-same-server-id = 0

# Bases a repliquer
replicate-do-db = ".$root->getAttribute('db')."

# Bases a 'binloguer'
binlog_do_db = ".$root->getAttribute('db');

      // If server is designed as "first", it wiil have the primary keys starting from 2, increment of 2
      switch ($myServer->getAttribute('first')) {
        
        case 'y':
          $conf = $commonConf."

log-bin = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logbinfile')."
log-bin-index = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logbinindex')."
log-error = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logerror')."

relay-log = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relaylogfile')."
relay-log-info-file = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relayloginfofile')."
relay-log-index = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relaylogindex')."

# Premier serveur de replication (clefs paires)
server-id = 1
auto-increment_increment = 2
auto_increment_offset = 2

####### Fin replication circulaire #######\n";
          break;
          
        // Else, "second" server, primary keys starting from 1, increment of 2
        case 'n':
          $conf = $commonConf."

log-bin = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logbinfile')."
log-bin-index = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logbinindex')."
log-error = ".$myServer->getElementsByTagName('logbin')->item(0)->getAttribute('logerror')."

relay-log = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relaylogfile')."
relay-log-info-file = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relayloginfofile')."
relay-log-index = ".$myServer->getElementsByTagName('relaylog')->item(0)->getAttribute('relaylogindex')."

# Second serveur de replication (clefs impaires)
server-id = 2
auto-increment_increment = 2
auto_increment_offset = 1

####### Fin replication circulaire #######\n";
          break;
          
        default:
          echo exec("clear");
          cecho("Unknown value of \"first\" attribute.", "red");
          echo "\n\n";
          menu();
      }
      
      // If we want to modify my.cnf
      switch ($method['method']) {
        case 'mysqld':
          if ($myServer->getAttribute('localhost') == "y") {
            $fileContent = file($method['filename']);
          }
          else {
            $fileContent = file($method['filename'].".".$myServer->getAttribute('ip').".new");
          }
          
          // Wen comment bind-address, skip-networking, max_binlog_size (in order to replace it)
          // and we get the line position of [mysqld]
          foreach ($fileContent as $lineNumber=>$lineContent) {
            if (preg_match("/^\s*bind-address/i", $lineContent)) {
              $fileContent[$lineNumer] = str_replace("bind-address", "# bind-address", $lineContent);
            }
            else {
              if (preg_match("/^\s*skip-networking/i", $lineContent)) {
                $fileContent[$lineNumer] = str_replace("skip-networking", "# skip-networking", $lineContent);
              }
              else {
                if (preg_match("/^\s*\[mysqld\]/i", $lineContent)) {
                  $mysqldPOS = $lineNumber;
                }
                else if (preg_match("/^\s*\max_binlog_size/i", $lineContent)) {
                  $fileContent[$lineNumber] = str_replace("max_binlog_size", "# max_binlog_size", $lineContent);
                }
              }
            }
          }
          
          // We change the configuration into the file
          $mysqldConf = array_splice($fileContent, $mysqldPOS + 1);
          array_unshift($mysqldConf, $conf);
          array_splice($fileContent, $mysqldPOS + 1, count($fileContent), implode("", $mysqldConf));
          
          check_errs(
            file_put_contents($method['filename'].".".$myServer->getAttribute('ip').".new", $fileContent),
            false,
            "Unable to write ".$method['filename'].".".$myServer->getAttribute('ip').".new",
            $method['filename'].".".$myServer->getAttribute('ip').".new file successfully written!"
          );
          echo "\n";
          cecho(
            "Please, replace the original configuration file by the .new file created. ".
            "Then restart MySQL, before running the SQL queries.", "red"
          );
          break;
          
        // If we just want to generate a TXT file
        case 'file':
          check_errs(
            file_put_contents($method['filename'], $conf),
            false,
            "Unable to write into ".$method['filename'].".",
            "File ".$method['filename']." successfully written!"
          );
          echo "\n";
          cecho(
            "Please, replace the original configuration file by the .new file created. ".
            "Then restart MySQL, before running the SQL queries.", "red"
          );
          break;
          
        default:
          echo exec("clear");
          cecho("Unknown method.", "red");
          echo "\n\n";
          menu();
      }
      break;
      
    // Else, quit
    default:
      echo exec("clear");
      cecho("You must have two servers in your XML file.", "red");
      echo "\n\n";
      menu();
  }
}

/**
  * Run SQL queries
  * 
  * @param string $file Used configuration file
  * @param string $name Server name
  * 
  * @return None
  */
function runSQLQueries($file, $name) {
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
  
  // Get the root node
  $root = $dom->documentElement;
  
  echo "\n";
  // Get the list of servers already in file
  $serverList = $dom->getElementsByTagName('serveur');
  switch ($serverList->length) {
    // If two, we can continue
    case 2:
      // Get the server
      $myServer = getServerByName($name, $file);
      
      // Get the other server
      $myOtherServer = getOtherServerByName($name, $file);
      
      // Get the SHOW MASTER STATUS of the other server because we need for CHANGE MASTER TO...
      if ($myOtherServer->getAttribute('localhost') == "y") {
        $masterStatus = getFileAndPosition(
          "localhost:".$myOtherServer->getAttribute('dbport'),
          $myOtherServer->getAttribute('dbusername'),
          $myOtherServer->getAttribute('dbpassword')
        );
      }
      else {
        $masterStatus = getFileAndPosition(
          $myOtherServer->getAttribute('ip').":".$myOtherServer->getAttribute('dbport'),
          $myOtherServer->getAttribute('dbusername'),
          $myOtherServer->getAttribute('dbpassword')
        );
      }
      check_errs($masterStatus, false, "Unable to get information.", "Information collected!");
      
      // SQL first query to execute on server
      $sql1 = "GRANT REPLICATION SLAVE ON *.* TO '".
        $myServer->getElementsByTagName('esclave')->item(0)->getAttribute('username').
        "'@'%' IDENTIFIED BY '".$myServer->getElementsByTagName('esclave')->item(0)->getAttribute('password')."';
FLUSH PRIVILEGES;";

      // SQL second query
      $sql2 = "SLAVE STOP;
CHANGE MASTER TO MASTER_HOST='".$myOtherServer->getAttribute('ip').
      "', MASTER_USER='".$myOtherServer->getElementsByTagName('esclave')->item(0)->getAttribute('username').
      "', MASTER_PASSWORD='".$myOtherServer->getElementsByTagName('esclave')->item(0)->getAttribute('password').
      "', MASTER_LOG_FILE='".$masterStatus['File']."', MASTER_LOG_POS=".$masterStatus['Position'].";
START SLAVE;";

      // Ask confirmation for the first query
      echo "\n";
      cecho("Do you want to execute these queries on ".$myServer->getAttribute('ip')." ?", "red");
      echo "\n\n";
      echo $sql1."\n\n";
      $answerSQL = recup("y or n [default y]: ", "y");
      if ($answerSQL != "n") {
        if ($myServer->getAttribute('localhost') == "y") {
          // Execute query as "localhost"
          executeSQLRequest(
            "localhost:".$myServer->getAttribute('dbport'),
            $myServer->getAttribute('dbusername'),
            $myServer->getAttribute('dbpassword'),
            $sql1
          );
        }
        else {
          // Execute distant query
          executeSQLRequest(
            $myServer->getAttribute('ip').":".$myServer->getAttribute('dbport'),
            $myServer->getAttribute('dbusername'),
            $myServer->getAttribute('dbpassword'),
            $sql1
          );
        }
      }
      
      // Ask confirmation for the second query
      echo "\n";
      cecho("Do you want to execute these queries on ".$myServer->getAttribute('ip')." ?", "red");
      echo "\n\n";
      echo $sql2."\n\n";
      $answerSQL = recup("y or n [default y]: ", "y");
      
      if ($answerSQL != "n") {
        if ($myServer->getAttribute('localhost') == "y") {
          // Execute query as "localhost"
          executeSQLRequest(
            "localhost:".$myServer->getAttribute('dbport'),
            $myServer->getAttribute('dbusername'),
            $myServer->getAttribute('dbpassword'),
            $sql2
          );
        }
        else {
          // Execute distant query
          executeSQLRequest(
            $myServer->getAttribute('ip').":".$myServer->getAttribute('dbport'),
            $myServer->getAttribute('dbusername'),
            $myServer->getAttribute('dbpassword'),
            $sql2
          );
        }
      }
      
      // Ask confirmation for mysqldump query
      echo "\n";
      // If server if the "first", we do the mysqldump
      if ($myServer->getAttribute('first') == "y") {
        cecho("Do you want to execute this command on ".$myServer->getAttribute('ip')." ?", "red");
        echo "\n\n";
        echo "mysqldump -u ".$myServer->getAttribute('dbusername').
          " -p --databases ".$root->getAttribute('db')." > /tmp/mysqldump.sql"."\n\n";
        $answerSQL = recup("y or n [default y]: ", "y");
        
        if ($answerSQL != "n") {
          // If localhost, local command
          if ($myServer->getAttribute('localhost') == "y") {
            exec(
              "mysqldump -u ".$myServer->getAttribute('dbusername')." -P ".$myServer->getAttribute('dbport').
              " -p".$myServer->getAttribute('dbpassword')." --databases ".
              $root->getAttribute('db')." > /tmp/mysqldump.sql",
              $result,
              $returnVar
            );
          }
          // Else, SSH
          else {
            // User allowed
            $sshUser = recup("Username allowed to connect to ".$myServer->getAttribute('ip').": ");
            // Command via SSH
            exec(
              "ssh ".$sshUser."@".$myServer->getAttribute('ip')." mysqldump -u ".
              $myServer->getAttribute('dbusername')." -P ".$myServer->getAttribute('dbport').
              " -p".$myServer->getAttribute('dbpassword')." --databases ".
              $root->getAttribute('db')." > /tmp/mysqldump.sql",
              $result,
              $returnVar
            );
          }
          check_errs($returnVar, true, "Unable to perform MySQLDump.", "MySQLDump performed!");
        }
        // If server is the "second", we get the mysqldump of the "first"
      }
      else {
        cecho("Do you want to execute this command on ".$myServer->getAttribute('ip')." ?", "red");
        echo "\n\n";
        // /tmp/mysqldump.sql is a local file
        echo "mysql -u ".$myServer->getAttribute('dbusername')." -p ".
        $root->getAttribute('db')." < /tmp/mysqldump.sql"."\n\n";
        $answerSQL = recup("y or n [default y]: ", "y");
        
        if ($answerSQL != "n") {
          if ($myServer->getAttribute('localhost') == "y") {
            exec(
              "mysql -u ".$myServer->getAttribute('dbusername')." -P ".$myServer->getAttribute('dbport')
              ." -p".$myServer->getAttribute('dbpassword')." < /tmp/mysqldump.sql",
              $result,
              $returnVar
            );
          }
          else {
            // User allowed
            $sshUser = recup("Username allowed to connect to ".$myServer->getAttribute('ip').": ");
            // Command via SSH
            exec(
              "ssh ".$sshUser."@".$myServer->getAttribute('ip')." mysql -u ".$myServer->getAttribute('dbusername').
              " -P ".$myServer->getAttribute('dbport')." -p".$myServer->getAttribute('dbpassword').
              " < /tmp/mysqldump.sql",
              $result,
              $returnVar
            );
          }
          check_errs($returnVar, true, "Failed.", "Success!");
        }
      }
      break;
      
    // Else, quit
    default:
      echo exec("clear");
      cecho("You must have two servers in your XML file.", "red");
      echo "\n\n";
      menu();
  }
}

/**
 * Get the two servers name
 * 
 * @param string $file Used configuration file
 * 
 * @return array
 */
function getServersName($file) {
  // Check the file
  check_file($file);
  
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  check_errs($dom->load($file), false, "File ".$file." not correctly formed.", "File ".$file." is OK!");
  
  // Get the root node
  $root = $dom->documentElement;
  
  // Get the list of servers already in file
  $serverList = $dom->getElementsByTagName('serveur');
  
  $return = null;
  foreach ($serverList as $oneServer) {
    if ($oneServer->getAttribute('first') == "y") {
      $return['firstServer'] = $oneServer->getAttribute('nom');
    }
    else {
      $return['secondServer'] = $oneServer->getAttribute('nom');
    }
  }
  
  return $return;
}

/**
 * Get the server DOMElement object from a name and a XML file
 * 
 * @param string $name Server name
 * @param string $file Used configuration file
 * 
 * @return DOMElement
 */
function getServerByName($name, $file) {
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  $dom->load($file);
  
  // Get the root node
  $root = $dom->documentElement;
  
  // Get the server from the XML file
  $serverList = $dom->getElementsByTagName('serveur');
  foreach ($serverList as $aServer) {
    if ($aServer->getAttribute('nom') == $name) {
      $myServer = $aServer;
    }
  }
  check_errs(
    isset($myServer),
    false,
    "Unable to find the server ".$name." in the ".$file." file.",
    "Server ".$name." found!"
  );
  
  return $myServer;
}

/**
 * Get the other server DOMElement object than a name and a XML file
 * 
 * @param string $name Server name
 * @param string $file Used configuration file
 * 
 * @return DOMElement
 */
function getOtherServerByName($name, $file) {
  // Create a new DOMDocument object
  $dom = new DOMDocument();
  
  // Load the XML file
  $dom->load($file);
  
  // Get the root node
  $root = $dom->documentElement;
  
  // Get the server from the XML file
  $serverList = $dom->getElementsByTagName('serveur');
  foreach ($serverList as $aServer) {
    if ($aServer->getAttribute('nom') != $name) {
      $myServer = $aServer;
    }
  }
  check_errs(
    isset($myServer),
    false,
    "Unable to find the other server than ".$name." in the ".$file." file.",
    "Server ".$aServer->getAttribute('nom')." found!"
  );
  
  return $myServer;
}

/**
 * Check if an XML file is valid and well-formed
 * 
 * @param string $file XML file
 * 
 * @return None 
 */
function check_file($file) {
  // Check if the file exists and is readable
  if (file_exists($file)) {
    check_errs(is_readable($file), false, "File ".$file." is not readable.", "File ".$file." readable!");
  }
  else {
    check_errs(touch($file), false, "File ".$file." not created.", "File ".$file." created!");
    file_put_contents($file, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?><serveurs db=\"\"></serveurs>");
  }
}

/**
 * Get binlog filename and its position
 * 
 * @param string $hostname   DB hostname
 * @param string $DBUser     DB username
 * @param string $DBPassword DB user password
 * 
 * @return array
 */
function getFileAndPosition($hostname, $DBUser, $DBPassword) {
  $connection = mysql_connect($hostname, $DBUser, $DBPassword);
  
  if ($connection) {
    if ($result = mysql_query("SHOW MASTER STATUS")) {
      $masterStatus = array();
      
      while ($row = mysql_fetch_object($result)) {
        $masterStatus['File'] = $row->File;
        $masterStatus['Position'] = intval($row->Position);
        mysql_free_result($result);
        mysql_close($connection);
        
        return $masterStatus;
      }
    }
    
    mysql_free_result($result);
    mysql_close($connection);
    
    return 0;
  }
  
  return 0;
}

/**
 * Execute SQL requests, separated by ;
 * 
 * @param string $hostname DB hostname
 * @param string $username DB username
 * @param string $password DB user password
 * @param string $sql      SQL query
 * 
 * @return None
 */
function executeSQLRequest($hostname, $username, $password, $sql) {
  $connection = mysql_connect($hostname, $username, $password);
  check_errs($connection, false, "Unable to connect", "Successfully connected!");
  $queries = explode(";", $sql);
  
  foreach ($queries as $query) {
    if ($query != "") {
      $result = mysql_query(trim($query));
      check_errs($result, false, "Unable to perform the request", "Query OK!");
    }
  }
  
  mysql_close($connection);
}

/**
 * Prompt a question and get the response
 * 
 * @param string $ask     Question to ask for
 * @param string $default [optional] Default response
 * 
 * @return string
 */
function recup($ask, $default = false) {
  echo $ask;
  $answer = trim(fgets(STDIN));
  
  if ($default && $answer === "") {
    return $default;
  }
  
  return $answer;
}

/**
 * Check if error occurs
 * 
 * @param string $commandResult Return code of a command
 * @param string $failureCode   Specify the supposed failure code
 * @param string $failureText   Text to show if failure
 * @param string $successText   Text to show if success
 * 
 * @return bool
 */
function check_errs($commandResult, $failureCode, $failureText, $successText) {
  cecho(">> status: ", "", "bold", "");
  if (is_null($failureCode)) {
    if (is_null($commandResult)) {
      cecho("ERROR : ".$failureText, "red", "", "");
      echo "\n\n";
      die();
    }
    
    cecho($successText);
    echo "\n";
    return 1;
  }
  else if ($commandResult == $failureCode) {
    cecho("ERROR : ".$failureText, "red", "", "");
    echo "\n\n";
    die();
  
    cecho($successText);
    echo "\n";
    return 1;
  }
}

/**
 * Announce a script
 * 
 * @param string $scriptName Name of the script
 * 
 * @return None
 */
function announce_script($scriptName) {
  cecho(" --- ".$scriptName." (".date("l d F H:i:s").") ---", "white", "bold", "red");
  echo "\n";
}

/**
 * Print information about a script
 * 
 * @param string $info Text to print
 * 
 * @return None 
 */
function info_script($info) {
  cecho(">> info: ".$info, "", "bold");
  echo "\n";
}

/**
 * Print a text with color, font style and background color
 * 
 * @param string $message    Text to print
 * @param string $color      [optional] Color of the text
 * @param string $style      [optional] Font style
 * @param string $background [optional] Background color
 * 
 * @return None
 */
function cecho($message, $color = "default", $style = "default", $background = "default") {
  $text = "<c c=".$color." s=".$style." bg=".$background.">".$message."</c>";
  echo parseShColorTag($text);
}

/**
 * To put colors in a CLI PHP script
 * Only on UNIX
 * Based from http://www.phpcs.com/codes/AJOUTER-COULEUR-VOS-BASH-PHP_45564.aspx
 * Some of styles don't work on all clients
 * 
 * @param string $text     [optional] Text to color
 * @param string $txtColor [optional] Wanted color (black, red, green, cyan, magenta, etc.)
 * @param string $bgColor  [optional] Background color
 * @param string $styleTxt [optional] Font style (bold, underline, reverse, flashing)
 * 
 * @return string
 */
function shColorText($text = '', $txtColor = '', $bgColor = '', $styleTxt = 'none') {
  $__ESC = "\033";
  $__START = "[";
  $__END = "m";
  
  $__CLEAR = $__ESC."[2J";
  $__NORMAL = $__ESC."[0m";
  
  if ($text === 'CLEAR') {
    return $__NORMAL.$__CLEAR;
  }
  
  if (empty($text) || !$text) {
    return $__NORMAL;
  }
  
  // Text color
  $aTextColor['black']   = 30; 
  $aTextColor['red']     = 31; 
  $aTextColor['green']   = 32; 
  $aTextColor['yellow']  = 33; 
  $aTextColor['blue']    = 34; 
  $aTextColor['magenta'] = 35; 
  $aTextColor['cyan']    = 36; 
  $aTextColor['white']   = 37; 
  
  // Background color
  $aBgColor['black']   = 40; 
  $aBgColor['red']     = 41; 
  $aBgColor['green']   = 42; 
  $aBgColor['yellow']  = 43; 
  $aBgColor['blue']    = 44; 
  $aBgColor['magenta'] = 45; 
  $aBgColor['cyan']    = 46; 
  $aBgColor['white']   = 47; 
  
  // Style text
  $aStyle['none']      = 0;   //normal
  $aStyle['bold']      = 1;   //gras
  $aStyle['underline'] = 4; //souligné
  $aStyle['flashing']  = 5; //clignotant
  $aStyle['reverse']   = 7;   //inversé
  
  $c = $__ESC.$__START;

  $a = null;

  if ($styleTxt && isset($aStyle[$styleTxt])) {
    $a[] = $aStyle[$styleTxt];
  }
  
  if ($txtColor && isset($aTextColor[$txtColor])) {
    $a[] = $aTextColor[$txtColor];
  }
  
  if ($bgColor && isset($aBgColor[$bgColor])) {
    $a[] = $aBgColor[$bgColor];
  }
  
  if (is_null($a)) {
    return $text;
  }

  $c = $__ESC.$__START.join(';', $a).$__END;
  
  return $c.$text.$__NORMAL;
}

/**
* Permet de mettre en forme la police d'un texte par des balises
*
* ex : Ceci est un <c c=blue bg=white s=bold>TEST</c>
*
**/

/**
 * Enable you to set font style with tags
 * Ex: This is a <c c=blue bg=white s=bold>TEST</c>
 * 
 * @param string $str String to set font style
 * 
 * @return string
 */
function parseShColorTag($str) {
  $tag = "/(<c[^>]*>)([^<]*)<\/c>/";
  $innerTag = "/([\w]+)=([\w]+)/";
  preg_match_all($tag, $str, $r); 
  
  if (!is_array($r[1])) {
    return $str;
  }
  
  foreach ($r[1] as $k => $v) {
    preg_match_all($innerTag, $v, $r2);
    
    if (!is_array($r2[1])) {
      return $str;
    }
    
    $c = $bg = $s = false;
    
    while (list($i,$value)=each($r2[1])) {
      switch($value) {
        case 'c':
          $c = $r2[2][$i];
          break;
          
        case 'bg':
          $bg = $r2[2][$i];
          break;
        
        case 's':
          $s = $r2[2][$i];
          break;
      }
    }
    
    $string = shColorText($r[2][$k], $c, $bg, $s);
    $str    = str_replace($r[0][$k], $string, $str);
    
  }
  return $str;
}

/**
 * In order to have a password prompt that works on many OS (works on Unix, Windows XP and Windows 2003 Server)
 * Source : http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
 * 
 * @param object $prompt [optional] Text to prompt
 * 
 * @return string
 */
function prompt_silent($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
      $vbscript, 'wscript.echo(InputBox("'.addslashes($prompt).'", "", "password here"))'
    );
    $command = "cscript //nologo " . escapeshellarg($vbscript);
    $password = rtrim(shell_exec($command));
    unlink($vbscript);
        
    return $password;
  }
  else {
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
