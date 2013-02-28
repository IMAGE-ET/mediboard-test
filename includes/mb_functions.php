<?php
/**
 * General purpose functions that haven't been namespaced (yet).
 *
 * @package    Mediboard
 * @subpackage includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Id$
 */

/**
 * Returns the CMbObject with given GET params keys, if it doesn't exist, a redirect is made
 *
 * @param string $class_key The class name of the object
 * @param string $id_key    The object ID
 * @param string $guid_key  The object GUID (classname-id)
 *
 * @return CMbObject The object loaded or nothing
 **/
function mbGetObjectFromGet($class_key, $id_key, $guid_key = null) {
  $object_class = CValue::get($class_key);
  $object_id    = CValue::get($id_key);
  $object_guid  = "$object_class-$object_id";

  if ($guid_key) {
    $object_guid = CValue::get($guid_key, $object_guid);
  }

  $object = CMbObject::loadFromGuid($object_guid);

  // Redirection
  if (!$object || !$object->_id) {
    global $ajax;
    CAppUI::redirect(
      "ajax=$ajax" .
      "&suppressHeaders=1".
      "&m=system".
      "&a=object_not_found".
      "&object_guid=$object_guid"
    );
  }

  return $object;
}

/**
 * Returns the CMbObject with given GET or SESSION params keys,
 * if it doesn't exist, a redirect is made
 *
 * @param string $class_key The class name of the object
 * @param string $id_key    The object ID
 * @param string $guid_key  The object GUID (classname-id)
 *
 * @return CMbObject The object loaded or nothing
 **/
function mbGetObjectFromGetOrSession($class_key, $id_key, $guid_key = null) {
  $object_class = CValue::getOrSession($class_key);
  $object_id    = CValue::getOrSession($id_key);
  $object_guid  = "$object_class-$object_id";

  if ($guid_key) {
    $object_guid = CValue::getOrSession($guid_key, $object_guid);
  }

  $object = CMbObject::loadFromGuid($object_guid);

  // Redirection
  if (!$object || !$object->_id) {
    global $ajax;
    CAppUI::redirect(
      "ajax=$ajax".
      "&suppressHeaders=1".
      "&m=system".
      "&a=object_not_found".
      "&object_guid=$object_guid"
    );
  }

  return $object;
}

/**
 * String to bool swiss knife
 *
 * @param mixed $value Any value, preferably string
 *
 * @return bool
 */
function toBool($value) {
  if (!$value) {
    return false;
  }

  return $value === true || preg_match('/^on|1|true|yes$/i', $value);
}

/**
 * Calculate the bank holidays in France
 *
 * @param date $date The relative date, used to calculate the bank holidays of a specific year
 *
 * @deprecated Use CMbDT instead
 * @return array List of bank holidays as dates
 **/
function mbBankHolidays($date = null) {
  return CMbDT::bankHolidays($date);
}

/**
 * Calculate the number of work days in the given month date
 *
 * @param date $date The relative date of the months to get work days
 *
 * @deprecated Use CMbDT instead
 * @return integer Number of work days
 **/
function mbWorkDaysInMonth($date = null) {
  return CMbDT::workDaysInMonth($date);
}

/**
 * Transforms absolute or relative time into a given format
 *
 * @param string $relative A relative time
 * @param string $ref      An absolute time to transform
 * @param string $format   The data in which the date will be returned
 *
 * @deprecated Use CMbDT instead
 * @return string The transformed date
 **/
function mbTransformTime($relative, $ref, $format) {
  return CMbDT::transform($relative, $ref, $format);
}

/**
 * Transforms absolute or relative time into DB friendly DATETIME format
 *
 * @param string   $relative Modifies the time (eg '+1 DAY')
 * @param datetime $ref      The reference date time fo transforms
 *
 * @deprecated Use CMbDT instead
 * @return string The transformed time
 **/
function mbDateTime($relative = null, $ref = null) {
  return CMbDT::dateTime($relative, $ref);
}

/**
 * Transforms absolute or relative time into XML DATETIME format
 *
 * @param string   $relative Modifies the time (eg '+1 DAY')
 * @param datetime $ref      The reference date time fo transforms
 *
 * @deprecated Use CMbDT instead
 * @return string The transformed time
 **/
function mbXMLDateTime($relative = null, $ref = null) {
  return CMbDT::dateTimeXML($relative, $ref);
}

/**
 * Converts an xs;duration XML duration into a DB friendly DATETIME
 *
 * @param string $duration Duration with format P1Y2M3DT10H30M0S
 *
 * @deprecated Use CMbDT instead
 * @return string The DATETIME, null if failed
 **/
function mbDateTimeFromXMLDuration($duration) {
  return CMbDT::dateTimeFromXMLDuration($duration);
}

/**
 * Transforms absolute or relative time into DB friendly DATE format
 *
 * @param String $relative The relative time vs the $ref (ex: "-1 MONTH")
 * @param Date   $ref      The reference date
 *
 * @deprecated Use CMbDT instead
 * @return Date The transformed time
 **/
function mbDate($relative = null, $ref = null) {
  return CMbDT::date($relative, $ref);
}

/**
 * Transforms absolute or relative time into DB friendly TIME format
 *
 * @param string   $relative The relative time vs the $ref (ex: "-1 MONTH")
 * @param datetime $ref      The reference date
 *
 * @deprecated Use CMbDT instead
 * @return time The transformed time
 **/
function mbTime($relative = null, $ref = null) {
  return CMbDT::time($relative, $ref);
}

/**
 * Counts the number of intervals between reference and relative
 *
 * @param time $from     From time
 * @param time $to       To time
 * @param time $interval Interval time
 *
 * @deprecated Use CMbDT instead
 * @return int Number of intervals
 **/
function mbTimeCountIntervals($from, $to, $interval) {
  return CMbDT::timeCountIntervals($from, $to, $interval);
}

/**
 * Retrieve nearest time (Dirac-like) with intervals
 *
 * @param time $reference     Reference time
 * @param time $mins_interval Minutes count
 *
 * @deprecated Use CMbDT instead
 * @return time Nearest time
 **/
function mbTimeGetNearestMinsWithInterval($reference, $mins_interval) {
  return CMbDT::timeGetNearestMinsWithInterval($reference, $mins_interval);
}

/**
 * Add a relative time to a reference time
 *
 * @param time $relative The relative time to add
 * @param time $ref      The reference time
 *
 * @deprecated Use CMbDT instead
 * @return string: the resulting time
 **/
function mbAddTime($relative, $ref = null) {
  return CMbDT::addTime($relative, $ref);
}

/**
 * Substract a relative time to a reference time
 *
 * @param time $relative The relative time to substract
 * @param time $ref      The reference time
 *
 * @deprecated Use CMbDT instead
 * @return string: the resulting time
 **/
function mbSubTime($relative, $ref = null) {
  return CMbDT::subTime($relative, $ref);
}

/**
 * Add a relative time to a reference datetime
 *
 * @param time     $relative The relative time to add
 * @param datetime $ref      The reference datetime
 *
 * @deprecated Use CMbDT instead
 * @return string the resulting time
 **/
function mbAddDateTime($relative, $ref = null) {
  return CMbDT::addDateTime($relative, $ref);
}

/**
 * Count days between two datetimes
 *
 * @param datetime $from From datetime
 * @param datetime $to   To datetime
 *
 * @deprecated Use CMbDT instead
 * @return int Days count
 **/
function mbDaysRelative($from, $to) {
  return CMbDT::daysRelative($from, $to);
}

/**
 * Count hours between two datetimes
 *
 * @param datetime $from From datetime
 * @param datetime $to   To datetime
 *
 * @deprecated Use CMbDT instead
 * @return int Days count
 **/
function mbHoursRelative($from, $to) {
  return CMbDT::hoursRelative($from, $to);
}

/**
 * Count minutes between two datetimes
 *
 * @param datetime $from From datetime
 * @param datetime $to   To datetime
 *
 * @deprecated Use CMbDT instead
 * @return int Days count
 **/
function mbMinutesRelative($from, $to) {
  return CMbDT::minutesRelative($from, $to);
}

/**
 * Compute time duration between two datetimes
 *
 * @param datetime $from   From date
 * @param datetime $to     To date
 * @param string   $format Format for time (sprintf syntax)
 *
 * @deprecated Use CMbDT instead
 * @return string hh:mm:ss diff duration
 **/
function mbTimeRelative($from, $to, $format = "%02d:%02d:%02d") {
  return CMbDT::timeRelative($from, $to, $format);
}

/**
 * Tell whether date is lunar
 *
 * @param date $date Date to check
 *
 * @deprecated Use CMbDT instead
 * @return boolean
 **/
function isLunarDate($date) {
  return CMbDT::isLunarDate($date);
}

/**
 * Convert a date from ISO to locale format
 *
 * @param string $date Date in ISO format
 *
 * @deprecated Use CMbDT instead
 * @return string Date in locale format
 */
function mbDateToLocale($date) {
  return CMbDT::dateToLocale($date);
}

/**
 * Convert a date from locale to ISO format
 *
 * @param string $date Date in locale format
 *
 * @deprecated Use CMbDT instead
 * @return string Date in ISO format
 */
function mbDateFromLocale($date) {
  return CMbDT::dateFromLocale($date);
}

/**
 * Convert a datetime from LDAP to ISO format
 *
 * @param string $dateLargeInt nano seconds (yes, nano seconds) since jan 1st 1601
 *
 * @deprecated Use CMbDT instead
 * @return string DateTime in ISO format
 */
function mbDateTimeFromLDAP($dateLargeInt) {
  return CMbDT::dateTimeFromLDAP($dateLargeInt);
}

/**
 * Convert a datetime from ActiveDirecetory to ISO format
 *
 * @param string $dateAD Datetime from AD since jan 1st 1601
 *
 * @deprecated Use CMbDT instead
 * @return string DateTime in ISO format
 */
function mbDateTimeFromAD($dateAD) {
  return CMbDT::dateTimeFromAD($dateAD);
}

/**
 * URL to the mediboard.org documentation page
 *
 * @param string $module Module name
 * @param string $action Action name
 *
 * @return string The URL to the requested page
 */
function mbPortalURL($module, $action = null) {
  if ($module == "tracker") {
    return CAppUI::conf("issue_tracker_url");
  }

  $url = CAppUI::conf("help_page_url");
  if (!$url || strpos($url, "%m") === false || strpos($url, "%a") === false) {
    return;
  }

  $pairs = array(
    "%m" => $module,
    "%a" => $action,
  );

  return strtr($url, $pairs);
}

/**
 * Check whether a string is NOT empty, to be used as a filter callback
 *
 * @param string $string String to check
 *
 * @return bool
 * @deprecated cf. CMbArray
 */
function stringNotEmpty($string){
  return $string !== "";
}

/**
 * Get a string containing loaded Dojo components for storage purposes
 *
 * @return string
 */
function mbLoadScriptsStorage(){
  $scripts = "";
  $scripts .= CJSLoader::loadFile("lib/dojo/dojo.js");
  $scripts .= CJSLoader::loadFile("lib/dojo/src/io/__package__.js");
  $scripts .= CJSLoader::loadFile("lib/dojo/src/html/__package__.js");
  $scripts .= CJSLoader::loadFile("lib/dojo/src/lfx/__package__.js");
  $scripts .= CJSLoader::loadFile("includes/javascript/storage.js");
  return $scripts;
}

/**
 * Set memory limit alternative with a minimal value approach
 * Shoud *always* be used
 *
 * @param string $limit Memory limit with ini_set() syntax
 *
 * @return string The old value on success, false on failure
 * @TODO : DELETE if not called anymore (check for all modules)
 */
function set_min_memory_limit($limit) {
  return CApp::setMemoryLimit($limit);
}

/**
 * Check whether a method is overridden in a given class
 *
 * @param mixed  $class  The class or object
 * @param string $method The method name
 *
 * @return bool
 */
function is_method_overridden($class, $method) {
  $reflection = new ReflectionMethod($class, $method);
  return $reflection->getDeclaringClass()->getName() == $class;
}

/**
 * Strip slashes recursively if value is an array
 *
 * @param mixed $value The value to be stripped
 *
 * @return mixed the stripped value
 **/
function stripslashes_deep($value) {
  return is_array($value) ?
    array_map("stripslashes_deep", $value) :
    stripslashes($value);
}

/**
 * Copy the hash array content into the object as properties
 * Only existing properties of are filled, when defined in hash
 *
 * @param array  $hash    The input hash
 * @param object &$object The object to feed
 *
 * @return void
 **/
function bindHashToObject($hash, &$object) {
  // @TODO use property_exists() which is a bit faster
  // BUT requires PHP >= 5.1

  $vars = get_object_vars($object);
  foreach ($hash as $k => $v) {
    if (array_key_exists($k, $vars)) {
      $object->$k = $hash[$k];
    }
  }
}

/**
 * Check if a number is a valid Luhn number
 * see http://en.wikipedia.org/wiki/Luhn
 *
 * @param string $code String representing a potential Luhn number
 *
 * @return bool
 */
function luhn ($code) {
  $code = preg_replace('/\D|\s/', '', $code);
  $code_length = strlen($code);
  $sum = 0;

  $parity = $code_length % 2;

  for ($i = $code_length - 1; $i >= 0; $i--) {
    $digit = $code{$i};

    if ($i % 2 == $parity) {
      $digit *= 2;

      if ($digit > 9) {
        $digit -= 9;
      }
    }

    $sum += $digit;
  }

  return (($sum % 10) == 0);
}

/**
 * Check wether a URL exists (200 HTTP Header)
 *
 * @param string $url    URL to check
 * @param string $method HTTP method (GET, POST, HEAD, PUT, ...)
 *
 * @return bool
 */
function url_exists($url, $method = null) {
  $old = ini_set('default_socket_timeout', 5);

  if ($method) {
    // By default get_headers uses a GET request to fetch the headers.
    // If you want to send a HEAD request instead,
    // you can change method with a stream context
    stream_context_set_default(
      array(
        'http' => array(
          'method' => $method
        )
      )
    );
  }

  $headers = @get_headers($url);
  ini_set('default_socket_timeout', $old);
  return (preg_match("|200|", $headers[0]));
}

/**
 * Forge an HTTP POST query
 *
 * @param string $url  Destination URL
 * @param mixed  $data Array or object containing properties
 *
 * @return bool
 */
function http_request_post($url, $data) {
  $data_url = http_build_query($data);
  $data_length = strlen($data_url);
  $options = array(
    "https" => array(
      "method" => "POST",
      "header"=> array(
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: $data_length",
        "User-Agent: ".$_SERVER["HTTP_USER_AGENT"]
      ),
      "content" => $data_url
    )
  );

  $context = stream_context_create($options);
  $content = file_get_contents($url, false, $context);
  return $content;
}

/**
 * Check response time from a web server
 *
 * @param string $url  Server URL
 * @param string $port Server port
 *
 * @return int Response time in milliseconds
 */
function url_response_time($url, $port) {
  $parse_url = parse_url($url);
  if (isset($parse_url["port"])) {
    $port = $parse_url["port"];
  }

  $url = isset($parse_url["host"]) ? $parse_url["host"] : $url;

  $starttime     = microtime(true);
  $file          = @fsockopen($url, $port, $errno, $errstr, 5);
  $stoptime      = microtime(true);

  if (!$file) {
    $response_time = -1;  // Site is down
  }
  else {
    fclose($file);
    $response_time = ($stoptime - $starttime) * 1000;
    $response_time = floor($response_time);
  }

  return $response_time;
}

/**
 * Build a url string based on components in an array
 * (see PHP parse_url() documentation)
 *
 * @param array $components Components, as of parse_url
 *
 * @return string
 */
function make_url($components) {
  $url = $components["scheme"] . "://";

  if (isset($components["user"])) {
    $url .= $components["user"] . ":" . $components["pass"] . "@";
  }

  $url .=  $components["host"];

  if (isset($components["port"])) {
    $url .=  ":" . $components["port"];
  }

  $url .=  $components["path"];

  if (isset($components["query"])) {
    $url .=  "?" . $components["query"];
  }

  if (isset($components["fragment"])) {
    $url .=  "#" . $components["fragment"];
  }

  return $url;
}

/**
 * Check wether a IP address is in intranet-like form
 *
 * @param string $ip IP address to check
 *
 * @return bool
 */
function is_intranet_ip($ip) {
  // ipv6 en local
  if ($ip === '::1' || $ip === '0:0:0:0:0:0:0:1') {
    return true;
  }

  $ip = explode('.', $ip);

  return
    ($ip[0] == 127) ||
    ($ip[0] == 10) ||
    ($ip[0] == 172 && $ip[1] >= 16 && $ip[1] < 32) ||
    ($ip[0] == 192 && $ip[1] == 168);
}

/**
 * Retrieve a server value from multiple sources
 *
 * @param string $key Value key
 *
 * @return string
 */
function get_server_var($key) {
  if (isset($_SERVER[$key])) {
    return $_SERVER[$key];
  }

  if (isset($_ENV[$key])) {
    return $_ENV[$key];
  }

  if (getenv($key)) {
    return getenv($key);
  }

  if (function_exists('apache_getenv') && apache_getenv($key, true)) {
    return apache_getenv($key, true);
  }
}

/**
 * Get browser remote IPs using most of available methods
 *
 * @return array Array with proxy, client and remote keys as IP adresses
 */
function get_remote_address() {
  $address = array(
    "proxy" => null,
    "client" => null,
    "remote" => null,
  );

  $address["client"] = ($client = get_server_var("HTTP_CLIENT_IP")) ? $client : get_server_var("REMOTE_ADDR");
  $address["remote"] = $address["client"];

  $forwarded = array(
    "HTTP_X_FORWARDED_FOR",
    "HTTP_FORWARDED_FOR",
    "HTTP_X_FORWARDED",
    "HTTP_FORWARDED",
    "HTTP_FORWARDED_FOR_IP",
    "X_FORWARDED_FOR",
    "FORWARDED_FOR",
    "X_FORWARDED",
    "FORWARDED",
    "FORWARDED_FOR_IP",
  );

  $client = null;

  foreach ($forwarded as $name) {
    if ($client = get_server_var($name)) {
      break;
    }
  }

  if ($client) {
    $address["proxy"]  = $address["client"];
    $address["client"] = $client;
  }

  // To handle weird IPs sent by iPhones, in the form "10.10.10.10, 10.10.10.10"
  $proxy  = explode(",", $address["proxy"]);
  $client = explode(",", $address["client"]);
  $remote = explode(",", $address["remote"]);

  $address["proxy"]  = reset($proxy);
  $address["client"] = reset($client);
  $address["remote"] = reset($remote);

  return $address;
}

/**
 * CRC32 alternative handling 32bit platform limitations
 *
 * @param string $data The data
 *
 * @return int CRC32 checksum
 */
function mb_crc32($data) {
  $crc = crc32($data);

  // if 32bit platform
  if (PHP_INT_MAX <= pow(2, 31)-1 && $crc < 0) {
    $crc += pow(2, 32);
  }

  return $crc;
}

/**
 * Initialize custom error handler
 *
 * @return void
 */
function build_error_log() {
  if (!is_file(LOG_PATH)) {
    $initTime = date("Y-m-d H:i:s");
    $logInit = "<h2>Log de Mediboard ré-initialisés depuis $initTime</h2>
      <script>
        function toggle_info(anchor) {
          var style = anchor.parentNode.getElementsByTagName('span')[0].style;
          style.display = style.display == 'none' ? '' : 'none';
          return false;
        }
       </script>
    ";
    file_put_contents(LOG_PATH, $logInit);
  }
}