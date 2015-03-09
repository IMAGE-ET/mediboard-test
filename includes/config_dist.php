<?php
/**
 * Global system and modules
 * WARNING: no config documentation in those files
 * Use instead locales for UI documentation
 *
 * @package    Mediboard
 * @subpackage includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Id$
 */

// Needed for module config file inclusions 
// Beginning of this file or installer will fail on config loading.
global $dPconfig;

// No trailing slash, no backslashes for Win users (use slashes instead)
$dPconfig["root_dir"]            = "/var/www/mediboard";
$dPconfig["company_name"]        = "mediboard.org";
$dPconfig["page_title"]          = "Mediboard SIH";
$dPconfig["base_url"]            = "http://localhost/mediboard/";
$dPconfig["master_key_filepath"] = "";

$dPconfig["offline"]           = "0";
$dPconfig["offline_non_admin"] = "0";
$dPconfig["instance_role"]     = "qualif";
$dPconfig["http_redirections"] = "0";
$dPconfig["mb_id"]             = "";
$dPconfig["mb_oid"]            = "";
$dPconfig["servers_ip"]        = "";
$dPconfig["system_date"]       = "";
$dPconfig["minify_javascript"] = "1";
$dPconfig["minify_css"]        = "1";
$dPconfig["currency_symbol"]   = "&euro;";
$dPconfig["ref_pays"]          = "1";
$dPconfig["hide_confidential"] = "0";
$dPconfig["locale_warn"]       = "0";
$dPconfig["locale_alert"]      = "^";
$dPconfig["debug"]             = "1";
$dPconfig["readonly"]          = "0";

// Logging
$dPconfig["log_js_errors"]            = "1";
$dPconfig["log_datasource_metrics"]   = "1";
$dPconfig["log_access"]               = "1";
$dPconfig["human_long_request_level"] = "10";
$dPconfig["bot_long_request_level"]   = "100";
$dPconfig["error_logs_in_db"]         = "0";

// Shared memory
$dPconfig["shared_memory"]             = "none";
$dPconfig["shared_memory_distributed"] = "";
$dPconfig["shared_memory_params"]      = "";

// Session
$dPconfig["session_handler"]            = "files";
$dPconfig["session_handler_mutex_type"] = "files";

// Mutex
$dPconfig["mutex_drivers"]        = array(
  "CMbRedisMutex" => "0",
  "CMbAPCMutex"   => "0",
  "CMbFileMutex"  => "1",
);
$dPconfig["mutex_drivers_params"] = array(
  "CMbRedisMutex" => "127.0.0.1:6379", // List of Redis servers
  "CMbFileMutex"  => "", // The folder that will contain the lock files
);

$dPconfig["weinre_debug_host"] = "";

$dPconfig["base_backup_lockfile_path"] = "";
$dPconfig["offline_time_start"]        = "";
$dPconfig["offline_time_end"]          = "";

$dPconfig["issue_tracker_url"] = "http://www.mediboard.org/public/tracker4";
$dPconfig["help_page_url"]     = "http://www.mediboard.org/public/mod-%m-tab-%a";

$dPconfig["csrf_protection"]     = "0";
$dPconfig["csrf_token_lifetime"] = "3600";
$dPconfig["purify_text_input"]   = "0";

$dPconfig["config_db"]          = "0";
$dPconfig["access_logs_buffer"] = "0";

// Dataminer limit
$dPconfig["dataminer_limit"] = "20";

// Object merge
$dPconfig["alternative_mode"]                = "1";
$dPconfig["merge_prevent_base_without_idex"] = "1";

$dPconfig["aio_output_path"] = "";

$dPconfig["browser_compat"] = array(
  'firefox' => '8.0',
  'msie'    => '8.0',
  'opera'   => '10.0',
  'chrome'  => '20.0',
  'safari'  => '525.26', // 3.2
);

// Object handlers
$dPconfig["object_handlers"] = array(
//  "CMyObjectHandler" => "1",
);

// Index handlers
$dPconfig["index_handlers"] = array(
//  "CMyIndexHandler" => "1",
);

// EAI handlers
$dPconfig["eai_handlers"] = array(
//  "CMyEAIHandler" => "1",
);

// Template placehodlers
$dPconfig["template_placeholders"] = array(
//  "CMyTemplatePlaceholder" => "1",
);

// Mode migration
$dPconfig["migration"]["active"]       = "0";
$dPconfig["migration"]["intranet_url"] = "http://intranet_server/mediboard/";
$dPconfig["migration"]["extranet_url"] = "http://extranet_server/mediboard/";
$dPconfig["migration"]["limit_date"]   = "1970-01-01";

// Time format
$dPconfig["date"]     = "%d/%m/%Y";
$dPconfig["time"]     = "%Hh%M";
$dPconfig["datetime"] = "%d/%m/%Y %Hh%M";
$dPconfig["longdate"] = "%A %d %B %Y";
$dPconfig["longtime"] = "%H heures %M minutes";
$dPconfig["timezone"] = "Europe/Paris";

// PHP config
$dPconfig["php"] = array();

// Standard database config
$dPconfig["db"]["std"] = array(
  "dbtype" => "mysql",     // Change to use another dbms
  "dbhost" => "localhost", // Change to connect to a distant Database
  "dbname" => "", // Change to match your Mediboard Database Name
  "dbuser" => "", // Change to match your Username
  "dbpass" => "", // Change to match your Password
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

// Module config file inclusion
$config_files = glob(dirname(__DIR__) . "/modules/*/config.php");
foreach ($config_files as $file) {
  include_once $file;
}
