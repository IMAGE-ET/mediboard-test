<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Thomas Despoix
 */


/********* 
 * Mediboard active configuration settings
 */
  
// Global hosting settings
$dPconfig["root_dir"] = "D:/Sites/Mediboard.org/demo";  // No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["company_name"] = "mediboard.org";
$dPconfig["page_title"] = "Mediboard : système de gestion des établissements de santé";
$dPconfig["base_url"] = "http://www.mediboard.org/demo";
$dPconfig["site_domain"] = "mediboard.org";

$dPconfig["dbtype"] = "mysql";   // ONLY MySQL is supported at present

// Standard database config
$dPconfig["baseMediboard"] = "std";
$dPconfig["db"]["std"] = array(
  "dbhost" => "localhost",  // Change to connect to a distant Database
  "dbname" => "mediboard", // Change to match your Mediboard Database Name
  "dbuser" => "mbadmin",    // Change to match your MySQL Username
  "dbpass" => "adminmb",    // Change to match your MySQL Password
  "dbport" => ""            // Change to match your Db Port or use the standard value of 3306 if string is empty
);

// Other databases config

// CIM10
$dPconfig["baseCIM10"] = "cim10";
$dPconfig["db"]["cim10"] = array(
  "dbhost" => "localhost",
  "dbname" => "cim10",
  "dbuser" => "CIM10Admin",
  "dbpass" => "AdminCIM10",
  "dbport" => ""
);

// CCAM
$dPconfig["db"]["ccam"] = array(
  "dbhost" => "localhost",
  "dbname" => "ccam",
  "dbuser" => "CCAMAdmin",
  "dbpass" => "AdminCCAM",
  "dbport" => ""
);

$dPconfig["baseCCAM"] = "ccamV2";
$dPconfig["db"]["ccamV2"] = array(
  "dbhost" => "localhost",
  "dbname" => "ccamV2",
  "dbuser" => "CCAMAdmin",
  "dbpass" => "AdminCCAM",
  "dbport" => ""
);

// GHS
$dPconfig["baseGHS"] = "GHS1010";
$dPconfig["db"]["GHS1010"] = array(
  "dbhost" => "localhost",
  "dbname" => "ghs1010",
  "dbuser" => "GHSAdmin",
  "dbpass" => "AdminGHS",
  "dbport" => ""
);

// Codes INSEE
$dPconfig["baseINSEE"] = "INSEE";
$dPconfig["db"]["INSEE"] = array(
  "dbhost" => "localhost",
  "dbname" => "INSEE",
  "dbuser" => "INSEEAdmin",
  "dbpass" => "AdminINSEE",
  "dbport" => ""
);

// Transit
$dPconfig["baseTransit"] = "Transit";
$dPconfig["db"]["Transit"] = array(
  "dbhost" => "localhost",
  "dbname" => "Transit",
  "dbuser" => "TransitAdmin",
  "dbpass" => "AdminTransit",
  "dbport" => ""
);

// you can add as much databases as you want by using
//$dPconfig["logical_name"] = "physical_name";
//$dPconfig["db"]["physical_name"] = array(
//  "dbhost" => "dbhost",
//  "dbname" => "dbname",
//  "dbuser" => "dbuser",
//  "dbpass" => "dbpass",
//  "dbport" => ""
//);

// Currency symbol (html entities allowed)
$dPconfig["currency_symbol"] = "&euro;";

// Enable if you want to hide confidentials fields
$dPconfig["hide_confidential"] = false;

// Enable if you want to have a demo version interface
$dPconfig["demo_version"] = false;

// Warn when a translation is not found (for developers and tranlators)
$dPconfig["locale_warn"] = false;

// The string appended to untranslated string or unfound keys
$dPconfig["locale_alert"] = "^";

// Set to true to help analyse errors
$dPconfig["debug"] = true;

// Use mediboard in a read-only mode
$dPconfig["readonly"] = false;

// Choose shared memory handler [none|eaccelerator] up to now
$dPconfig["shared_memory"] = "none";

/********* 
 * Mediboard module-specific active configuration settings
 */

// HPRIM export FTP settings
$dPconfig["dPinterop"]["hprim_export"] = array (
  "hostname" => "",
  "username" => "",
  "userpass" => "",
  "fileprefix" => "",
);

/********* 
 * Configuration settings to be removed with dotProject legacy purge
 */

// Check for legacy password (prior to dP 1.0 alpha)
$dPconfig["check_legacy_password"] = false;

// Set this value to true to use persistent database connections
$dPconfig["dbpersist"] = false;

/********* 
 * Legacy dotProject inactive configuration settings
 */

// Enable if you want to be able to see other users"s tasks
$dPconfig["show_all_tasks"] = false;

// Enable if you want to support gantt charts
$dPconfig["enable_gantt_charts"] = true;

// Sets the locale for the jpGraph library.  Leave blank if you experience problems
$dPconfig["jpLocale"] = "";

// Enable if you want to log changes using the history module
$dPconfig["log_changes"] = false;

// Enable if you want to check task"s start and end dates are non empty
$dPconfig["check_tasks_dates"] = true;

// The number of "working" hours in a day
$dPconfig["daily_working_hours"] = 8.0;

// Set to true if you need to be able to relink tickets to
// an arbitrary parent.  Useful for email-generated tickets,
// but the interface is a bit clunky.
$dPconfig["link_tickets_kludge"] = false;

// Calendar settings.
$dPconfig["cal_day_start"]     = 8;           // Start hour, in 24 hour format
$dPconfig["cal_day_end"]       = 17;          // End hour in 24 hour format
$dPconfig["cal_day_increment"] = 15;          // Increment, in minutes
$dPconfig["cal_working_days"]  = "1,2,3,4,5"; // days of week that the company works 0=Sunday

// Compatibility mode
$dPconfig["interop"]["mode_compat"] = "default";
$dPconfig["interop"]["base_url"]    = "http://myserver/mypage";

// File parsers to return indexing information about uploaded files
$dPconfig["ft"] = array(
  "default" => "/usr/bin/strings",
  "application/msword" => "/usr/bin/strings",
  "text/html" => "/usr/bin/strings",
  "application/pdf" => "/usr/bin/pdftotext",
);

$dPconfig["dPplanningOp"]["operation"] = array (
  "duree_deb"        => "0",
  "duree_fin"        => "10",
  "hour_urgence_deb" => "0",
  "hour_urgence_fin" => "23",
  "min_intervalle"   => "15"
);
$dPconfig["dPplanningOp"]["sejour"] = array (
  "heure_deb"      => "0",
  "heure_fin"      => "23",
  "min_intervalle" => "15"
);

$dPconfig["dPcabinet"]["addictions"] = 0;
$dPconfig["dPcabinet"]["CPlageConsult"] = array (
  "hours_start"      => "8",
  "hours_stop"       => "20",
  "minutes_interval" => "15"
);

$dPconfig["dPImeds"]["url"] = "http://10.100.0.67/listedossiers.aspx";

$dPconfig["dPfiles"]["nb_upload_files"] = "1";

$dPconfig["dPsante400"] = array (
  "nb_rows" => "5",
  "mark_row" => "0",
  "dsn" => "",
  "user" => "",
  "pass" => "",
  "group_id" => "",
);

?>