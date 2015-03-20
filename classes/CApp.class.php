<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The actual application class
 * Responsibilities:
 *  - application kill
 *  - class management
 *  - file inclusion
 *  - memory and performance
 */
class CApp {
  static $inPeace    = false;
  static $encoding   = "utf-8";
  static $classPaths = array();
  static $is_robot   = false;
  static $message    = null;

  /** @var string Current request unique identifier */
  private static $requestUID = null;
  
  /* 
   * The order of the keys is important (only the first keys 
   * are displayed in the short view of the Firebug console).
   */
  static $performance = array(
    // Performance
    "genere"  => null,
    "memoire" => null,
    "size"    => null,
    "objets"  => 0,
    "ip"      => null,
    
    // Errors
    "error"   => 0,
    "warning" => 0,
    "notice"  => 0,
    
    // Cache
    "cachableCount"  => null,
    "cachableCounts" => null,
    
    // Objects
    "objectCounts" => null,
    
    // Function cache
    "functionCache" => null,

    // Data source information
    "dataSource"     => null,
    "dataSourceTime" => null,
  );

  /**
   * @var Chronometer Main application chronometer
   */
  static $chrono;
    
  static $handlers;
  
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectedly
   * 
   * @return void
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      if (!headers_sent()) {
        header("HTTP/1.1 500 Application died unexpectedly");
      }
      CModelObject::error("Application-died-unexpectedly");
    }
  }
  
  /**
   * Make application die properly
   * 
   * @return void
   */
  static function rip() {
    // If the client doesn't support cookies, we destroy its session
    // Sometimes, the cookie is empty while the client support cookies (url auth in IE)
    /*if (empty($_COOKIE)) {
      CSessionHandler::end();
    }*/

    //access log
    include "./includes/access_log.php";
    // Long request log
    include "./includes/long_request_log.php";

    if (CAppUI::$token_restricted || CAppUI::$auth_info && CAppUI::$auth_info->restricted) {
      CSessionHandler::end(true);
    }
    else {
      // Explicit close of the session before object destruction
      CSessionHandler::writeClose();
    }

    self::$inPeace = true;
    die;
  }

  /**
   * Go to the "offline" page, specifying a a reason
   *
   * @param string $reason The reason: maintenance, db-access, db-backup
   *
   * @return void
   */
  static function goOffline($reason = null){
    switch ($reason) {
      default:
      case "maintenance":
        self::$message = "Le système est désactivé pour cause de maintenance.";
        break;

      case "db-access":
        self::$message = "La base de données n'est pas accessible.";
        break;

      case "db-backup":
        self::$message = "La base de données est en cours de sauvegarde.";
        break;
    }

    include __DIR__."/../offline.php";

    self::rip();
  }
  
  /**
   * Apply a ratio multiplicator to current memory limit
   * 
   * @param float $ratio Ratio to apply
   * 
   * @return int Previous memory limit
   */
  static function memoryRatio($ratio) {
    $limit = CMbString::fromDecaSI(ini_get("memory_limit"), "") * $ratio;
    $limit = CMbString::toDecaSI($limit, "");
    return ini_set("memory_limit", $limit);
  }

  /**
   * Set time limit in seconds
   *
   * @param integer $seconds The time limit in seconds
   *
   * @return string
   */
  static function setTimeLimit($seconds) {
    return self::setMaxPhpConfig("max_execution_time", $seconds);
  }

  /**
   * Set memory limit in megabytes
   *
   * @param string $megabytes The memory limit, suffixed with K, M, G
   *
   * @return string
   */
  static function setMemoryLimit($megabytes) {
    return self::setMaxPhpConfig("memory_limit", $megabytes);
  }

  /**
   * set a php configuration limit with a minimal value
   * if the value is < actual, the old value is used
   *
   * @param string     $config the php parameter
   * @param string|int $limit  the limit required
   *
   * @return string
   */
  static function setMaxPhpConfig($config, $limit) {
    $actual = CMbString::fromDecaBinary(ini_get($config));
    $new    = CMbString::fromDecaBinary($limit);

    //new value is superior => change the config
    if ($new > $actual) {
      return ini_set($config, $limit);
    }

    return ini_get($config);
  }


  /**
   * Redirect to empty the POST data, 
   * so that it is not posted back when refreshing the page.
   * Use it instead of CApp::rip() directly
   * 
   * @param bool $redirect Try to redirect if true
   * 
   * @return void
   */
  static function emptyPostData($redirect = true){
    if ($redirect && !empty($_POST) && !headers_sent()) {
      CAppUI::redirect(/*CValue::read($_SERVER, "QUERY_STRING")*/);
    }
    self::rip();
  }
  
  /**
   * Outputs JSON data after removing the Output Buffer, with a custom mime type
   * 
   * @param object|array $data     The data to output
   * @param string       $mimeType [optional] The mime type of the data, application/json by default
   * 
   * @return void
   */
  static function json($data, $mimeType = "application/json") {
    ob_clean();
    header("Content-Type: $mimeType");
    echo json_encode($data);
    self::rip();
  }

  /**
   * Fetch an HTML content of a module view, as a HTTP GET call would do
   * Very useful to assemble multiple views
   *
   * @param string $module    The module name or the file path
   * @param string $file      [optional] The file of the module, or null
   * @param array  $arguments [optional] The GET arguments
   *
   * @return string The fetched content
   */
  static function fetch($module, $file = null, $arguments = array()) {
    $save = array();
    foreach ($arguments as $_key => $_value) {
      if (!isset($_GET[$_key])) {
        continue;
      }
      
      $save[$_key] = $_GET[$_key];
    }
    
    foreach ($arguments as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    ob_start();
    if (isset($file)) {
      include "./modules/$module/$file.php";
    }
    else {
      include $module;
    }
    $output = ob_get_clean();
   
    foreach ($save as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    return $output;
  }
  
  /**
   * Get the base application URL
   * 
   * @return string The URL
   */
  static function getBaseUrl(){
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host = $_SERVER["SERVER_NAME"];
    $port = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $path = dirname($_SERVER["SCRIPT_NAME"]);
    
    return $scheme."://".$host.$port.$path;
  }
  
  /**
   * Include all the classes of the framework and modules
   * 
   * @return void
   */
  static function includeAllClasses() {
    $root_dir = CAppUI::conf("root_dir");
    
    // Ordered paths
    $dirs = array(
      // Require all global classes
      "classes/*.class.php",
      "classes/*/*.class.php",

      // Require mobile classes
      "mobile/*/*.class.php",

      // Require all modules classes
      // Don't include classes in subdirectories (there are a lot !)
      "modules/*/classes/*.class.php",
      "modules/*/classes/*/*.class.php",
      //"modules/*/classes/*/*/*.class.php",

      // Require all modules setups 
      "modules/*/setup.php",
    );

    // Actual requires
    foreach ($dirs as $dir) {
      $files = glob("$root_dir/$dir");
      foreach ($files as $fileName) {
        include_once $fileName;
      }
    }
  }

  /**
   * Return all child classes of a given class having given properties
   *
   * @param string $parent        [optional] Parent class
   * @param bool   $active_module [optional] If true, filter on active modules
   *
   * @return array Class names
   * @todo Default parent class should probably be CModelObject
   */
  static function getChildClasses($parent = "CMbObject", $active_module = false) {
    $cache = new Cache(__METHOD__, func_get_args(), Cache::INNER_OUTER);
    if ($cache->exists()) {
      return $cache->get();
    }
    
    self::includeAllClasses();
    
    $classes = get_declared_classes();
    foreach ($classes as $key => $class) {
      // Filter on parent class
      if ($parent && !is_subclass_of($class, $parent)) {
        unset($classes[$key]);
        continue;
      }
      
      // Filter on active module
      if ($active_module) {
        $object = new $class; 
        if (!isset($object->_ref_module)) {
          unset($classes[$key]);
        }
      }
    }
    
    sort($classes);
    
    return $cache->put($classes, true);
  }
  
  /**
   * Return all CMbObject child classes
   * 
   * @param array &$instances [optional] If not null, retrieve an array of all object instances
   * 
   * @return array Class names
   */
  static function getMbClasses(&$instances = null) {
    $classes = self::getChildClasses("CStoredObject");
    
    foreach ($classes as $key => $class) {
      // In case we removed a class and it's still in the cache
      if (!class_exists($class, true)) {
        unset($classes[$key]);
        continue;
      }
      
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
     
      // Instanciated class?
      // @todo All class should be instanciable 
      if (!$object->_class) {
        unset($classes[$key]);
        continue;
      }
      
      $instances[$class] = $object;
    }
    
    return $classes;
  }
  
  /**
   * Return all storable CMbObject classes which module is installed
   *
   * @param array $classes [optional] Restrain to given classes
   * 
   * @return array Class names
   */
  static function getInstalledClasses($classes = array()) {
    if (empty($classes)) {
      $classes = self::getMbClasses();
    }
    
    foreach ($classes as $key => $class) {
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
      
      // Installed module ?
      if ($object->_ref_module === null) {
        unset($classes[$key]);
        continue;
      }
  
      // Storable class ?
      if (!$object->_spec->table) {
        unset($classes[$key]);
        continue;
      }
    }
    
    return $classes;
  }
  
  /**
   * Group installed classes by module names
   * 
   * @param array $classes Class names
   * 
   * @return array Array with module names as key and class names as values
   */
  static function groupClassesByModule($classes) {
    $grouped = array();
    foreach ($classes as $class) {
      $object = new $class;
      if ($module = $object->_ref_module) {
        $grouped[$module->mod_name][] = $class;
      }
    }
    return $grouped;
  }
  
  /**
   * Staticly build index handlers array
   * 
   * @return void
   */
  public static final function makeHandlers() {
    if (is_array(self::$handlers)) {
      return;
    }
    
    // Static initialisations
    self::$handlers = array();
    foreach (CAppUI::conf("index_handlers") as $_class => $_active) {      
      if ($_active) {
        if (!class_exists($_class)) {
          CModelObject::error("application-index-handler-missing-class%s", $_class);
          continue;
        }
        self::$handlers[$_class] = new $_class;
      }
    }
  }

  /**
   * Subject notification mechanism
   * TODO Implement to factorize
   *   on[Before|After][Store|Merge|Delete]()
   *   which have to get back de CPersistantObject layer
   *
   * @param string $message        The notification type
   * @param bool   $break_on_first Don't catch exceptions thrown by the handlers
   *
   * @return void
   */
  static function notify($message, $break_on_first = false) {
    $args = func_get_args();
    array_shift($args); // $message

    // Event Handlers
    self::makeHandlers();

    // If break on first, don't catch Exceptions
    if ($break_on_first) {
      foreach (self::$handlers as $_handler) {
        call_user_func_array(array($_handler, "on$message"), $args);
      }
    }

    // Else, catche exceptions
    else {
      foreach (self::$handlers as $_handler) {
        try {
          call_user_func_array(array($_handler, "on$message"), $args);
        }
        catch (Exception $e) {
          CAppUI::setMsg($e, UI_MSG_ERROR);
        }
      }
    }
  }

  /**
   * Try to approximate ouput buffer bandwidth consumption
   * Won't take into account output_compression
   *
   * @return int Number of bytes
   */
  static function getOuputBandwidth() {
    // Already flushed
    // @fixme output_compression ignored!!
    $bandwidth = CHTMLResourceLoader::$flushed_output_length;
    // Still something to be flushed ?
    // @fixme output_compression ignored!!
    $bandwidth += ob_get_length();

    return $bandwidth;
  }

  /** @var int Useful to log extra bandwidth use such as FTP transfers and so on */
  static $extra_bandwidth = 0;

  /**
   * Try to approximate non ouput buffer bandwidth consumption
   * Won't take into account output_compression
   *
   * @return int Number of bytes
   */
  static function getOtherBandwidth() {
    $bandwidth = 0;

    // Add REQUEST params, FILES params, request and response headers to the size of the hit
    // Use of http_build_query() to approximate HTTP serialization
    $bandwidth += strlen(http_build_query($_REQUEST));
    $bandwidth += strlen(http_build_query($_FILES));
    $bandwidth += strlen(http_build_query(apache_request_headers()));
    $bandwidth += strlen(http_build_query(apache_response_headers()));

    // Add actual FILES sizes to the size of the hit
    foreach ($_FILES as $_files) {
      $_files_size = $_files["size"];
      $bandwidth += is_array($_files_size) ? array_sum($_files_size) : $_files_size;
    }

    // Add extra bandwidth that may have been declared
    $bandwidth += self::$extra_bandwidth;

    return $bandwidth;
  }


  /**
   * Prepare performance data to be displayed
   *
   * @return void
   */
  static function preparePerformance(){
    arsort(CStoredObject::$cachableCounts);
    arsort(CStoredObject::$objectCounts);
    arsort(self::$performance["autoload"]);

    self::$performance["genere"]         = round(self::$chrono->total, 3);
    self::$performance["memoire"]        = CHTMLResourceLoader::getOutputMemory();
    self::$performance["objets"]         = CStoredObject::$objectCount;
    self::$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
    self::$performance["cachableCounts"] = CStoredObject::$cachableCounts;
    self::$performance["objectCounts"]   = CStoredObject::$objectCounts;
    self::$performance["ip"]             = $_SERVER["SERVER_ADDR"];

    self::$performance["size"] = CHTMLResourceLoader::getOutputLength();

    self::$performance["cache"] = array(
      "totals" => Cache::$totals,
      "total"  => Cache::$total,
    );

    $time = 0;

    // Data sources performance
    foreach (CSQLDataSource::$dataSources as $dsn => $ds) {
      if (!$ds) {
        continue;
      }

      $chrono      = $ds->chrono;
      $chronoFetch = $ds->chronoFetch;

      $time += $chrono->total + $chronoFetch->total;

      self::$performance["dataSources"][$dsn] = array(
        "count"      => $chrono->nbSteps,
        "time"       => $chrono->total,
        "countFetch" => $chronoFetch->nbSteps,
        "timeFetch"  => $chronoFetch->total,
      );
    }

    self::$performance["dataSourceTime"] = $time;
  }

  /**
   * Must be the same here and SHM::init
   * We don't use CApp because it can be called in /install
   *
   * @return string Application identifier, in a pool of servers
   */
  static function getAppIdentifier(){
    $root_dir = CAppUI::conf("root_dir");

    return preg_replace("/[^\w]+/", "_", $root_dir);
  }

  /**
   * Initializes a unique request ID to identify current request
   *
   * @return string
   */
  private static function initRequestUID(){
    $user_id = CUser::get()->_id;
    $uid     = uniqid("", true);

    $address = get_remote_address();
    $ip      = $address["remote"];

    // MD5 is enough as it doesn't have to be crypto proof
    self::$requestUID = md5("$user_id/$uid/$ip");
  }

  /**
   * Get the current request unique ID
   *
   * @return string
   */
  static function getRequestUID(){
    if (self::$requestUID === null) {
      self::initRequestUID();
    }

    return self::$requestUID;
  }

  /**
   * Execute a script on all servers
   *
   * @param String[] $get  Parameters GET
   * @param String[] $post Parameters POST
   *
   * @return Array
   */
  static function multipleServerCall($get, $post = null) {
    $base = $_SERVER["SCRIPT_NAME"]."?";
    foreach ($get as $_param => $_value) {
      $base .= "$_param=$_value&";
    }
    $base = substr($base, 0, -1);

    $address = array();
    $list_ip = trim(CAppUI::conf("servers_ip"));
    if ($list_ip) {
      $address = preg_split("/\s*,\s*/", $list_ip, -1, PREG_SPLIT_NO_EMPTY);
      $address = array_flip($address);
    }

    foreach ($address as $_ip => $_value) {
      $address[$_ip] = self::serverCall("http://$_ip$base", $post);
    }

    return $address;
  }

  /**
   * Send the request on the server
   *
   * @param String   $url  URL
   * @param String[] $post Parameters POST
   *
   * @return bool|string
   */
  static function serverCall($url, $post = null) {
    CSessionHandler::writeClose();
    global $rootName, $version;
    $session_name = preg_replace("/[^a-z0-9]/i", "", $rootName);
    $cookie = CValue::cookie($session_name);
    $result = array("code" => "", "body" => "");
    try {
      $http_client = new CHTTPClient($url);
      $http_client->setCookie("$session_name=$cookie");
      $http_client->setUserAgent("Mediboard-".$version["version"]);
      $http_client->setOption(CURLOPT_FOLLOWLOCATION, true);
      if ($post) {
        $request = $http_client->post(http_build_query($post));
      }
      else {
        $request = $http_client->get();
      }
    }
    catch (Exception $e) {
      CSessionHandler::start();
      $result["body"] = $e->getMessage();
      return $result;
    }
    CSessionHandler::start();

    $result["code"]   = $http_client->last_information["http_code"];
    $result["body"]   = $request;

    return $result;
  }

  static function getReleaseInfo() {
    $svn_status_file       = __DIR__."/../tmp/svnstatus.txt";
    $root_release_file     = __DIR__."/../release.xml";

    $applicationVersion = array(
      "releaseTitle" => null,
      "releaseDate"  => null,
      "releaseCode"  => null,
      "releaseRev"   => null,

      "revision"     => null,
      "date"         => null,
      "relative"     => null,
      "title"        => "",
    );

    // Release information
    if (is_readable($root_release_file)) {
      $releaseInfoDOM = new DOMDocument();
      $releaseInfoDOM->load($root_release_file);
      $releaseElement = $releaseInfoDOM->documentElement;

      $releaseCode = $releaseElement->getAttribute("code");
      list($year, $month) = explode("_", $releaseCode);
      $title = strftime("%B", mktime(0, 0, 0, $month, 10)) . " " . $year;

      $applicationVersion["releaseTitle"] = $title;
      $applicationVersion["releaseDate"]  = CMbDT::dateTimeFromXMLDuration($releaseElement->getAttribute("date"));
      $applicationVersion["releaseCode"]  = $releaseCode;
      $applicationVersion["releaseRev"]   = $releaseElement->getAttribute("rev");

      $applicationVersion["title"] = "Branche de ".$applicationVersion["releaseTitle"];
    }

    // Revision information
    if (is_readable($svn_status_file)) {
      $svnInfo = file($svn_status_file);
      $revision = array(
        "revision" => explode(": ", $svnInfo[0]),
        "date"     => explode(": ", $svnInfo[1]),
      );

      $applicationVersion["revision"] = trim($revision["revision"][1]);
      $applicationVersion["date"]     = CMbDT::dateTime(trim($revision["date"][1]));
      $applicationVersion["relative"] = CMbDate::relative($applicationVersion["date"]);

      $applicationVersion["title"] .= "\n".
        "Mise à jour le ".CMbDT::dateToLocale($applicationVersion["date"])."\n".
        "Révision : ".$applicationVersion["revision"];
    }

    return $applicationVersion;
  }
}
