<?php

require_once("utils.php");
require_once("Procedure.php");

function sendFileFTPProcedure( $backMenu ) {
  $procedure = new Procedure();
  
  $choice = "0";
  $procedure->showReturnChoice( $choice );
  
  $qt_hostname  = $procedure->createQuestion( "Hostname: " );
  $hostname     = $procedure->askQuestion( $qt_hostname );
  
  if ( $hostname === $choice ) {
    $procedure->clearScreen();
    $procedure->showMenu( $backMenu, true );
    exit();
  }
  
  $qt_username    = $procedure->createQuestion( "Username: " );
  $username       = $procedure->askQuestion( $qt_username );
  
  $password       = prompt_silent();
  
  $qt_file        = $procedure->createQuestion( "File: " );
  $file           = $procedure->askQuestion( $qt_file );
  
  $qt_port        = $procedure->createQuestion( "Port [default 21]: ", 21 );
  $port           = $procedure->askQuestion( $qt_port );
  
  $qt_passiveMode = $procedure->createQuestion( "Switch to passive mode [y or n, default n]? ", "n" );
  $passiveMode    = $procedure->askQuestion( $qt_passiveMode );
  
  $qt_ASCIIMode   = $procedure->createQuestion( "Switch to ASCII mode [y or n, default n]? ", "n" );
  $ASCIIMode      = $procedure->askQuestion( $qt_ASCIIMode );
  
  $commandLine = "php " . dirname(__FILE__) . "/sendFileFTP.php " . $hostname . " " . $username . " " . $password . " " . $file;
  
  if ($port != "") {
    $commandLine .= " -p " . $port;
  }

  if ($passiveMode == "y") {
    $commandLine .= " -m";
  }

  if ($ASCIIMode == "y") {
    $commandLine .= " -t";
  }
  
  echo "\n";
  echo shell_exec($commandLine) . "\n\n";
}

function sendFileFTPCall( $command, $argv ) {
  if (count($argv) == 7) {
    $hostname = $argv[0];
    $username = $argv[1];
    $password = $argv[2];
    $file = $argv[3];
    $port = $argv[4];
    $passiveMode = $argv[5];
    $ASCIIMode = $argv[6];
    
    $commandLine = "php " . dirname(__FILE__) . "/sendFileFTP.php " . $hostname . " " . $username . " " . $password . " " . $file;

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
    echo "\nUsage : $command sendfileftp <hostname> <username> <password> <file> options\n
<hostname>        : host to connect
<username>        : username requesting
<password>        : password of the user
<file>            : file to send\n
Options :
[<port>]          : port to connect, default 21
[<passive_mode>]  : switch to passive mode, default n
[<ascii_mode>]    : switch to ascii mode, default n\n\n";
    return 1;
  }
}
?>
