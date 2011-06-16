<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
  
// Needed for module config file inclusions 
// Beginning of this file or installer will fail on config loading.
global $dPconfig; 

// Global hosting settings
$dPconfig["root_dir"]          = "/var/www/mediboard";  // No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["company_name"]      = "mediboard.org";
$dPconfig["page_title"]        = "Mediboard SIH";
$dPconfig["base_url"]          = "http://localhost/mediboard/";

$dPconfig["offline"]           = "0";      // Offline mode (redirect to offline.php)
$dPconfig["instance_role"]     = "qualif"; // qualif|prod
$dPconfig["http_redirections"] = "0";      // 0 => no redirections, 1 => redirections actives
$dPconfig["mb_id"]             = "";       // Instance unique ID
$dPconfig["minify_javascript"] = "0";      // 0 => no concatenation, 1 => concatenation, 2 => concatenation+minification
$dPconfig["minify_css"]        = "0";      // 0 => no concatenation, 1 => concatenation, 2 => concatenation+minification
$dPconfig["currency_symbol"]   = "&euro;"; // Currency symbol (HTML entities allowed)
$dPconfig["hide_confidential"] = "0";      // Enable if you want to hide confidential fields
$dPconfig["locale_warn"]       = "0";      // Warn when a translation is not found (for developers and translators)
$dPconfig["locale_alert"]      = "^";      // The string appended to untranslated string or not found keys
$dPconfig["debug"]             = "1";      // Set to true to help analyze errors
$dPconfig["readonly"]          = "0";      // Read-only mode : any store will fail
$dPconfig["shared_memory"]     = "none";   // Shared memory handler [none|eaccelerator|apc]
$dPconfig["session_handler"]   = "files";  // Session handler [files|memcache|mysql|...]
$dPconfig["log_js_errors"]     = "1";      // Log JavaScript errors into the error log
$dPconfig["apache_child_terminate_memory_limit"] = ""; // The memory limit above which the child apache process will be terminated
// Object merge
$dPconfig["alternative_mode"]  = "0";      // Alternative fusion mode (keeps one object, max 2 objects to merge)
$dPconfig["merge_prevent_base_without_idex"]  = "1"; // Prevent choosing a base without an external ID

$dPconfig["browser_compat"]    = array(    // minimal browser versions
  'firefox' => '3.0',
  'msie'    => '8.0',
  'opera'   => '9.6',
  'chrome'  => '5.0',
  'safari'  => '525.26', // 3.2
);

// Object handlers
$dPconfig["object_handlers"]   = array (
//  "CMyObjectHandler" => true,
);

// Mode migration
$dPconfig["migration"]["active"] = "0";
$dPconfig["migration"]["intranet_url"] = "http://intranet_server/mediboard/";
$dPconfig["migration"]["extranet_url"] = "http://extranet_server/mediboard/";
$dPconfig["migration"]["limit_date"] = "1970-01-01";

// Time format
$dPconfig["date"]     = "%d/%m/%Y";
$dPconfig["time"]     = "%Hh%M";
$dPconfig["datetime"] = "%d/%m/%Y %Hh%M";
$dPconfig["longdate"] = "%A %d %B %Y";
$dPconfig["longtime"] = "%H heures %M minutes";
$dPconfig["timezone"] = "Europe/Paris";

// PHP config
$dPconfig["php"] = array(
  "memory_limit" => "128M"
);

// Standard database config
$dPconfig["db"]["std"] = array(
  "dbtype" => "mysql",     // Change to use another dbms
  "dbhost" => "localhost", // Change to connect to a distant Database
  "dbname" => "", // Change to match your Mediboard Database Name
  "dbuser" => "", // Change to match your Username
  "dbpass" => "", // Change to match your Password
);

// Other databases config

// CIM10
$dPconfig["db"]["cim10"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "cim10",
  "dbuser" => "CIM10Admin",
  "dbpass" => "AdminCIM10",
);

$dPconfig["db"]["ccamV2"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "ccamV2",
  "dbuser" => "CCAMAdmin",
  "dbpass" => "AdminCCAM",
);

// GHS
$dPconfig["db"]["GHS1010"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "ghs1010",
  "dbuser" => "GHSAdmin",
  "dbpass" => "AdminGHS",
);

// Codes INSEE
$dPconfig["db"]["INSEE"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "INSEE",
  "dbuser" => "INSEEAdmin",
  "dbpass" => "AdminINSEE",
);

// Transit
$dPconfig["db"]["Transit"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "Transit",
  "dbuser" => "TransitAdmin",
  "dbpass" => "AdminTransit",
);
  
// Compatibility mode
$dPconfig["interop"]["mode_compat"] = "default";

// File parsers to return indexing information about uploaded files
$dPconfig["ft"] = array(
  "default"            => "/usr/bin/strings",
  "application/msword" => "/usr/bin/strings",
  "text/html"          => "/usr/bin/strings",
  "application/pdf"    => "/usr/bin/pdftotext",
);

/********* 
 * Mediboard module-specific active configuration settings
 */

$dPconfig["dPadmissions"] = array (
  "fiche_admission" => "a4",
  "show_dh"         => "1",
);


$dPconfig["dPsalleOp"] = array(
  "mode_anesth"     => "0",
  "max_add_minutes" => "10",
  "max_sub_minutes" => "30",
  "COperation"      => array(
    "mode"        => "0",
    "modif_salle" => "0",
    "modif_actes" => "oneday",
  ),
  "CActeCCAM"       => array(
    "contraste"         => "0",
    "alerte_asso"       => "1",
    "tarif"             => "0",
    "codage_strict"     => "0",
    "signature"         => "0",
    "openline"          => "0",
    "modifs_compacts"   => "0",
    "commentaire"       => "1",
    "envoi_actes_salle" => "0",
  ),
  "CDossierMedical" => array (
    "DAS" => "0",
  ),
  "CReveil"         => array (
    "multi_tabs_reveil" => "1",
  ),
  "CDailyCheckList" => array(
    "active"              => "0",
    "active_salle_reveil" => "0"
  )
);

$dPconfig["dPbloc"]["CPlageOp"] = array (
  "hours_start"          => "8",
  "hours_stop"           => "20",
  "minutes_interval"     => "15",
  "plage_vide"           => "0",
  "libelle_ccam"         => "1", 
  "locked"               => "1",
  "days_locked"          => "0",
  "planning"             => array (
    "col1" => "interv",
    "col2" => "sejour",
    "col3" => "patient"
  ),
  "chambre_operation"    => "0",
  "impression_suivi"     => "0",
  "id_salles_impression" => "",
);

$dPconfig["dPsante400"] = array (
  "nb_rows" => "5",
  "mark_row" => "0",
  "cache_hours" => "1",
  "dsn" => "",
  "user" => "",
  "pass" => "",
  "group_id" => "",
	"CSejour" => array(
	  "sibling_hours" => 1,
	),
);

$dPconfig["dPlabo"] = array (
  "CCatalogueLabo" => array (
    "remote_name" => "LABO",
    "remote_url"  => "http://localhost/mediboard/modules/dPlabo/remote/catalogue.xml",
  ),
  
  "CPackExamensLabo" => array (
    "remote_url" => "http://localhost/mediboard/modules/dPlabo/remote/pack.xml",
  )
  ,
  "CPrescriptionLabo" => array (
    "url_ftp_prescription"    => "",
    "url_ws_id_prescription"  => "",
    "pass_ws_id_prescription" => "",
    "login_ftp_prescription"  => "",
    "pass_ftp_prescription"   => "",
  ),
);

$dPconfig["system"] = array(
  "phone_number_format" => "99 99 99 99 99",
  "reverse_proxy"  => "0.0.0.0",
  "website_url"    => "http://www.mediboard.org",
);

$dPconfig["hprim21"] = array (
  "CHprim21Reader" => array(
    "hostname"      => "",
    "username"      => "",
    "userpass"      => "",
    "fileextension" => "hpr",
  ),
);

$dPconfig["dPpmsi"] = array(
  "systeme_facturation" => "",
	"server"              => "0",
  "transmission_actes"  => "pmsi",
  "passage_facture"     => "envoi",
); 

$dPconfig["bloodSalvage"] = array (
    "inLivretTherapeutique" => "1",
    "AntiCoagulantList"     => "",
);

$dPconfig["dmi"] = array (
  "CDMI" => array(
    "product_category_id" => "",
    "active" => 0
  ),
  "CDM" => array(
    "product_category_id" => "",
    "active" => 0
  )
);

$dPconfig["mediusers"] = array (
  "tag_mediuser" => ""
);

// Module config file inclusion
$config_files = glob("./modules/*/config.php");
foreach ($config_files as $file) {
  require_once $file;
}

?>