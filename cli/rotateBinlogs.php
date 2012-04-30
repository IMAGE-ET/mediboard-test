<?php 
require_once ("utils.php");

function rotateBinlogs($userAdminDB, $passAdminDB, $binLogsDir, $binLogIndexFilename) {
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
  
  # Backup destination dir
  force_dir("/mbbackup");
  force_dir("/mbbackup/binlogs");
  $backup = "/mbbackup/binlogs";
  
  # Flush logs to start a new one
  if ($passAdminDB === "") {
    echo shell_exec("mysqladmin -u " . $userAdminDB . " flush-logs");
  }
  else {
    echo shell_exec("mysqladmin -u " . $userAdminDB . " -p" . $passAdminDB . " flush-logs");
  }
  
  # Move all logs but latest to backup
  $binLogFiles = file($binLogsDir . "/" . $binLogIndexFilename);
  check_errs($binLogFiles, false, "Impossible d'ouvrir le fichier " . $binLogsDir . "/" . $binLogIndexFilename, "Fichier " . $binLogsDir . "/" . $binLogIndexFilename . " ouvert !");
  
  $lastBinLogFile = trim(end($binLogFiles));
  
  $binPrefixPOS = strpos(basename($lastBinLogFile), ".");
  $binPrefix = substr(basename($lastBinLogFile), 0, $binPrefixPOS+1);

  foreach (glob(dirname($lastBinLogFile) . "/" . $binPrefix . "*") as $oneBinLogFile) {
    if ($oneBinLogFile != $lastBinLogFile) {
      rename($oneBinLogFile, $backup . "/" . basename($oneBinLogFile));
    }
  }
  
  # Move binlog indexes to binlog backup
  copy($binLogsDir . "/" . $binLogIndexFilename, $backup . "/" . $binLogIndexFilename);

  # Rotate binlogs and indeces for a week
  shell_exec("find $backup -name \"*bin.*\" -mtime +7 -exec rm -f {} \;");
  shell_exec("find $backup -name \"binlog-*.index\" -mtime +7 -exec rm -f {} \;");
}
?>
