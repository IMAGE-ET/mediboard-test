<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * General purpose configuration
 */
class CConfiguration extends CMbMetaObject {
  const INHERIT = "@@INHERIT@@";

  const STATUS_OK    = "ok";
  const STATUS_DIRTY = "dirty";
  const STATUS_EMPTY = "empty";

  public $configuration_id;

  public $feature;
  public $value;

  // The BIG config model
  private static $model_raw = array();
  private static $model = array();

  private static $values = array();
  private static $hosts = array();

  private static $dirty = null;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key      = "configuration_id";
    $spec->table    = "configuration";
    $spec->uniques["feature"] = array("feature", "object_class", "object_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["feature"]      = "str notNull";
    $props["value"]        = "str";
    $props["object_id"]    = "ref class|CMbObject meta|object_class"; // not notNull
    $props["object_class"] = "str class show|0"; // not notNull
    return $props;
  }

  /**
   * Returns the CMbObjectSpec object of the host object
   *
   * @return CMbObjectSpec
   */
  static function _getSpec(){
    static $spec;

    if (!isset($spec)) {
      $self = new self;
      $spec = $self->_spec;
    }

    return $spec;
  }

  /**
   * Register a configuration tree
   *
   * @param array $configs The config tree with the specs
   *
   * @return void
   */
  static function register($configs) {
    self::$model_raw = array_merge_recursive(self::$model_raw, $configs);
  }

  /**
   * Build the configuration tree
   *
   * @return void
   */
  static protected function buildTree() {
    foreach (self::$model_raw as $_inherit => $_tree) {
      $list = array();
      self::_buildConfigs($list, array(), $_tree);

      if (!isset(self::$model[$_inherit])) {
        self::$model[$_inherit] = array();
      }

      self::$model[$_inherit] = array_merge(self::$model[$_inherit], $list);
    }
  }

  /**
   * Build configuration subtree
   *
   * @param array  &$list List to fill
   * @param string $path  Configuration path
   * @param array  $tree  Model tree
   *
   * @return void
   */
  protected static function _buildConfigs(&$list, $path, $tree) {
    foreach ($tree as $key => $subtree) {
      $_path = $path;
      $_path[] = $key;

      // If a leaf (prop)
      if (is_string($subtree)) {
        // Build spec
        $parts = explode(" ", $subtree);

        $spec_options = array(
          "type"   => array_shift($parts),
          "string" => $subtree,
        );

        foreach ($parts as $_part) {
          $options = explode("|", $_part, 2);
          $spec_options[array_shift($options)] = count($options) ? $options[0] : true;
        }

        // Always have a default value
        if (!isset($spec_options["default"])) {
          $spec_options["default"] = "";
        }

        $list[implode(" ", $_path)] = $spec_options;
      }

      // ... else a subtree
      else {
        self::_buildConfigs($list, $_path, $subtree);
      }
    }
  }

  /**
   * Get hash of the data
   *
   * @param array $data The data to get the hash of (MD5 of serialization)
   *
   * @return string
   */
  static protected function _getHash($data) {
    return md5(serialize($data));
  }

  /**
   * Get model cache status
   *
   * @return string Can be self::STATUS_EMPTY, self::STATUS_DIRTY or self::STATUS_OK
   */
  static function getModelCacheStatus() {
    $model = SHM::get("config-model");

    if (!$model) {
      return self::STATUS_EMPTY;
    }

    if (self::_isModelCacheDirty($model["hash"])) {
      return self::STATUS_DIRTY;
    }

    return self::STATUS_OK;
  }

  /**
   * Get model cache update date
   *
   * @return null|string Model cache date or null if no cache
   */
  static protected function _getModelCacheDate(){
    $model = SHM::get("config-model");

    if ($model) {
      return $model["date"];
    }

    return null;
  }

  /**
   * Tells if the model cache is out of date
   *
   * @param string $hash The hash of the model in cache
   *
   * @return bool True if cache is out of date
   */
  static protected function _isModelCacheDirty($hash) {
    return $hash !== self::_getHash(self::$model_raw);
  }

  /**
   * Tells if the values cache is out of date
   *
   * @param string $date The date of the latest data update
   *
   * @return bool True if the values cache is out of date
   */
  static protected function _isValuesCacheDirty($date) {
    if (self::$dirty === false) {
      return false;
    }

    $model_status = self::getModelCacheStatus($date);

    if ($model_status !== self::STATUS_OK) {
      return self::$dirty = true;
    }

    $spec = self::_getSpec();

    $status_result = $spec->ds->loadHash("SHOW TABLE STATUS LIKE '{$spec->table}'");

    // database or model were updated
    return self::$dirty = $date <= $status_result["Update_time"] ||
                          $date <= self::_getModelCacheDate();
  }

  /**
   * Get the up to date model
   *
   * @param array $inherits An optional selection of inheritance paths
   *
   * @return array The up to date model
   */
  static function getModel($inherits = array()) {
    if (empty(self::$model)) {
      if (($model = SHM::get("config-model")) && !self::_isModelCacheDirty($model["hash"])) {
        self::$model = $model["content"];
      }
      else {
        self::buildTree();

        SHM::put(
          "config-model",
          array(
            "date"    => strftime(CMbDT::ISO_DATETIME), // Don't use CMbDT::dateTime because it may be offsetted
            "hash"    => self::_getHash(self::$model_raw),
            "content" => self::$model,
          )
        );
      }
    }

    if (!empty($inherits)) {
      if (!is_array($inherits)) {
        $inherits = array($inherits);
      }

      $subset = array();
      foreach ($inherits as $_inherit) {
        $subset[$_inherit] = self::$model[$_inherit];
      }

      return $subset;
    }

    return self::$model;
  }

  /**
   * Build all the configuration data
   *
   * @return void
   */
  static function buildAllConfig(){
    $inherits = array_keys(self::getModel());

    $values_flat = array();

    foreach ($inherits as $_inherit) {
      $tree = self::getObjectTree($_inherit);
      $all = array();
      self::_flattenObjectTree($all, $tree);

      $values_flat["global"] = self::getConfigs($_inherit);

      foreach ($all as $_object) {
        $_guid = $_object->_guid;

        if (!isset($values_flat[$_guid])) {
          $values_flat[$_guid] = array();
        }

        $values_flat[$_guid] = array_merge($values_flat[$_guid], self::getConfigs($_inherit, null, $_object));
      }
    }

    $values = array();
    $hosts = array();
    foreach ($values_flat as $_host => $_values) {
      $values[$_host] = self::unflatten($_values);
      $hosts[] = $_host;
    }

    self::$hosts = $hosts;
    self::$values = $values;
  }

  /**
   * Unflatten a dictionnary
   *
   * @param array  $array     The dictionnary
   * @param string $separator The separator
   *
   * @return array
   */
  static function unflatten(array $array, $separator = " ") {
    $tree = array();

    foreach ($array as $_key => $_value) {
      $_parts = explode($separator, $_key);

      $node =& $tree;
      foreach ($_parts as $_part) {
        $node =& $node[$_part];
      }

      $node = $_value;
    }

    return $tree;
  }

  /**
   * Refresh the data cache
   *
   * @return void
   */
  static function refreshDataCache(){
    $mutex = new CMbFileMutex("config-build");
    $mutex->acquire(10);

    // If cache was built by another thread
    if (SHM::exists("config-values-__HOSTS__")) {
      $mutex->release();

      $hosts_shm = SHM::get("config-values-__HOSTS__");
      $hosts = $hosts_shm["content"];

      $values = array();
      foreach ($hosts as $_host) {
        $_host_value = SHM::get("config-values-$_host");
        $values[$_host] = $_host_value["content"];
      }

      self::$values = $values;
      self::$hosts  = $hosts;

      self::$dirty = false;

      mbLog("'config-values' already present, skipping regeneration");

      return;
    }

    $t = microtime(true);

    self::buildAllConfig();

    $t1 = microtime(true) - $t;

    $datetime = strftime(CMbDT::ISO_DATETIME);

    foreach (self::$values as $_host => $_configs) {
      SHM::put(
        "config-values-$_host",
        array(
          "date"    => $datetime,
          "content" => $_configs,
        )
      );
    }

    SHM::put(
      "config-values-__HOSTS__",
      array(
        "date"    => $datetime,
        "content" => array_keys(self::$values),
      )
    );

    $t2 = microtime(true) - $t - $t1;

    mbLog(sprintf("'config-values' gerenated in %f ms, written in %f ms", $t1*1000, $t2*1000));

    $mutex->release();

    self::$dirty = false;
  }

  /**
   * Clear data cache
   *
   * @return void
   */
  static function clearDataCache() {
    SHM::remKeys("config-values-*");
    self::$values = array();
    self::$hosts = array();
  }

  /**
   * Get the config values for an object or for all objects
   *
   * @param string $object_guid The object to get config values of
   *
   * @return array|mixed The config values
   */
  static function getValues($object_guid){
    static $dirty = null;

    // Check values status, only once per request
    if ($dirty === null && self::getValuesCacheStatus() === self::STATUS_DIRTY) {
      self::refreshDataCache();
      $dirty = false;
    }

    // Check if all the keys exist
    if (empty(self::$hosts)) {
      $hosts = SHM::get("config-values-__HOSTS__");

      if (empty($hosts)) {
        self::refreshDataCache();
      }
      else {
        self::$hosts = $hosts["content"];
      }
    }

    // For a single host
    if (empty(self::$values[$object_guid])) {
      $_values = SHM::get("config-values-$object_guid");
      self::$values[$object_guid] = $_values["content"];
    }

    return self::$values[$object_guid];
  }

  /**
   * Get a specific value or a subtree of values
   *
   * @param string $object_guid The object to get config values of
   * @param string $feature     The configuration key to get
   *
   * @return array|string The configuration value or the subtree
   */
  static function getValue($object_guid, $feature) {
    $values = self::getValues($object_guid);

    return CMbArray::readFromPath($values, $feature);
  }

  /**
   * Get the values cache status
   *
   * @return string The values can be self::STATUS_EMPTY, self::STATUS_DIRTY or self::STATUS_OK
   */
  static function getValuesCacheStatus() {
    $hosts = SHM::get("config-values-__HOSTS__");

    if (!$hosts) {
      return self::STATUS_EMPTY;
    }

    if (self::_isValuesCacheDirty($hosts["date"])) {
      return self::STATUS_DIRTY;
    }

    return self::STATUS_OK;
  }

  /**
   * Flattend an object tree
   *
   * @param array &$all     The tree to flattend
   * @param array $children The children
   *
   * @return void
   */
  static protected function _flattenObjectTree(&$all, $children) {
    $all = array_merge($all, CMbArray::pluck($children, "object"));

    foreach ($children as $child) {
      self::_flattenObjectTree($all, $child["children"]);
    }
  }

  /**
   * Get module configurations
   *
   * @param string $module  The module
   * @param string $inherit The inherit schema
   *
   * @return array
   */
  static function getModuleConfigs($module = null, $inherit = null) {
    $model = self::getModel($inherit);

    if (!$module) {
      return $model;
    }

    $configs = array();
    $module_start = "$module ";

    foreach ($model as $_inherit => $_configs) {
      $_conf = array();

      foreach ($_configs as $_feature => $_spec) {
        if (strpos($_feature, $module_start) === false) {
          continue;
        }

        $_conf[$_feature] = $_spec;
      }

      $configs[$_inherit] = $_conf;
    }

    return $configs;
  }

  /**
   * Get class configs
   *
   * @param string $class   Class name
   * @param string $module  Module
   * @param string $inherit Inheritance schema
   * @param bool   $flatten Flatten output
   *
   * @return array
   */
  static function getClassConfigs($class, $module = null, $inherit = null, $flatten = true) {
    $configs = array();

    $model = self::getModuleConfigs($module, $inherit);

    $patterns = array("$class ", "$class.");

    foreach ($model as $_inherit => $_configs) {
      foreach ($patterns as $_patt) {
        $_inherit = self::_simplifyInherit($_inherit);

        // Faster than preg_match
        if ($_inherit === $class || strpos($_inherit, $_patt) !== false) {
          if ($flatten) {
            $configs = array_merge($configs, $_configs);
          }
          else {
            $configs[$_inherit] = $_configs;
          }
          break;
        }
      }
    }

    return $configs;
  }

  /**
   * Get the specs tree or flat array
   *
   * @param string $module      Module name
   * @param array  $config_keys Config keys to get the specs of
   * @param bool   $flatten     Flatten the specs tree or not
   *
   * @return array The specs tree or flat array
   */
  static function getConfigsSpecs($module = null, $config_keys = null, $flatten = true){
    $configs = array();
    $model = self::getModuleConfigs($module);

    if ($config_keys) {
      $config_keys = array_flip($config_keys);
    }

    foreach ($model as $_inherit => $_configs) {
      if ($flatten) {
        $configs = array_merge($configs, $_configs);

        if ($config_keys) {
          $configs = array_intersect_key($configs, $config_keys);
        }
      }
      else {
        if ($config_keys) {
          $_configs = array_intersect_key($_configs, $config_keys);
        }

        $configs[$_inherit] = $_configs;
      }
    }

    return $configs;
  }

  /**
   * Get the object tree
   *
   * @param string $inherit Inheritance path
   *
   * @return array The object tree
   */
  static function getObjectTree($inherit){
    $tree = array();

    $inherit = self::_simplifyInherit($inherit);
    $classes = explode(" ", $inherit);

    self::_getObjectTree($tree, $classes);

    return $tree;
  }

  /**
   * Simplify an inheritance schema, removing the prefix
   *
   * @param string $inherit The inheritance schema
   *
   * @return string
   */
  static protected function _simplifyInherit($inherit) {
    return preg_replace('@([\w ]+ / )@', "", $inherit);
  }

  /**
   * Recursive method to build the object tree
   *
   * @param array  &$subtree   Sub tree to fill
   * @param array  $classes    Classes or inheritance schema
   * @param string $parent_fwd Parent forward ref
   * @param int    $parent_id  Parent ID
   *
   * @return void
   */
  static protected function _getObjectTree(&$subtree, $classes, $parent_fwd = null, $parent_id = null) {
    static $cache = array();

    if (empty($classes)) {
      return;
    }

    $class = array_pop($classes);
    $_parts = explode(".", $class);

    $fwd = null;
    if (count($_parts) == 2) {
      list($class, $fwd) = $_parts;
    }

    $where = array();
    if ($parent_fwd && $parent_id) {
      $where[$parent_fwd] = "= '$parent_id'";
    }

    $_cache_key = "$parent_fwd-$parent_id-$class";
    if (isset($cache[$_cache_key])) {
      $_list = $cache[$_cache_key];
    }
    else {
      /** @var CMbObject $_obj */
      $_obj = new $class;

      // Attention il faut generer les configurations de TOUS les objets, donc ne pas utiliser loadListWitfPerms
      $_list = $_obj->loadList($where);
      $_list = self::naturalSort($_list, array("_view"));

      $cache[$_cache_key] = $_list;
    }

    foreach ($_list as $_object) {
      $subtree[$_object->_guid] = array(
        "object"   => $_object,
        "children" => array(),
      );

      self::_getObjectTree($subtree[$_object->_guid]["children"], $classes, $fwd, $_object->_id);
    }
  }

  /**
   * Get the configuration values of an object, without inheritance
   *
   * @param string  $object_class Object class
   * @param integer $object_id    Object ID
   * @param array   $config_keys  The keys of the values to get
   *
   * @return array The configuration values
   */
  static protected function getSelfConfig($object_class = null, $object_id = null, $config_keys = null) {
    static $cache_data = null;
    static $cache = array();

    $key = "$object_class/$object_id/".($config_keys ? implode("-", $config_keys) : "");
    if (isset($cache[$key])) {
      return $cache[$key];
    }

    if ($cache_data === null) {
      $spec = self::_getSpec();

      $request = new CRequest;
      $request->addTable($spec->table);
      $request->addSelect(array("feature", "value", "object_class", "object_id"));

      $cache_data = $spec->ds->loadList($request->makeSelect());
    }

    if ($object_class && $object_id) {
      $data = array_filter(
        $cache_data,
        function ($v) use ($object_class, $object_id) {
          return $v["object_class"] === $object_class && $v["object_id"] === $object_id;
        }
      );
    }
    else {
      $data = array_filter(
        $cache_data,
        function ($v) {
          return $v["object_class"] === null && $v["object_id"] === null;
        }
      );
    }

    if ($config_keys) {
      $data = array_filter(
        $data,
        function ($v) use ($config_keys) {
          return in_array($v["feature"], $config_keys);
        }
      );
    }

    $final_data = array();

    foreach ($data as $_data) {
      $final_data[$_data["feature"]] = $_data["value"];
    }

    return $cache[$key] = $final_data;
  }

  /**
   * Returns the default values
   *
   * @param array $config_keys The keys of the values to get
   *
   * @return array The values
   */
  static public function getDefaultValues($config_keys = null){
    $values = array();

    foreach (self::getConfigsSpecs(null, $config_keys) as $_feature => $_params) {
      $values[$_feature] = $_params["default"];
    }

    return $values;
  }

  /**
   * Get the usable configuration values of an object
   *
   * @param string    $config_inherit The inheritance path
   * @param array     $config_keys    The keys to get the configuration value of
   * @param CMbObject $object         Object
   *
   * @return array The corresponding configs
   */
  static function getConfigs($config_inherit, $config_keys = null, CMbObject $object = null){
    $ancestor_configs = self::getAncestorsConfigs($config_inherit, $config_keys, $object);

    $configs = array();

    foreach ($ancestor_configs as $_ancestor) {
      $configs = array_merge($configs, $_ancestor["config"]);
    }

    return $configs;
  }

  /**
   * Get the model subtree from an inherit path
   *
   * @param string $feature Inherit path
   *
   * @return int|null|string
   */
  static function lookupInherit($feature) {
    foreach (self::getModel() as $_inherit => $_config) {
      if (isset($_config[$feature])) {
        return $_inherit;
      }
    }

    return null;
  }

  /*
  static function getConfig($feature, CMbObject $object = null) {
    $inherit = self::lookupInherit($feature);

    $configs = self::getConfigs($inherit, array($feature), $object);

    return reset($configs);
  }*/

  /**
   * Get all the configs for an inheritance schema, with all the inherited values
   *
   * @param string         $config_inherit Inheritance schema
   * @param array|null     $config_keys    Configuration keys to get, or null
   * @param CMbObject|null $object         Host object, if none, we'll get global values
   *
   * @return array Configuration values
   */
  static function getAncestorsConfigs($config_inherit, $config_keys = null, CMbObject $object = null) {
    $configs = array();

    $parent_config = self::getDefaultValues($config_keys);

    $configs[] = array(
      "object"        => "default",
      "config"        => $parent_config,
      "config_parent" => $parent_config,
    );

    $configs[] = array(
      "object"        => "global",
      "config"        => self::getSelfConfig(null, null, $config_keys),
      "config_parent" => $parent_config,
    );

    if ($object) {
      $ancestors = array();
      $config_inherit_parts = explode(" ", $config_inherit);

      $fwd = null;
      $prev_object = $object;
      foreach ($config_inherit_parts as $i => $class) {
        $class_fwd = explode(".", $class);

        // Never need the fwd field for the first item
        if ($i == 0) {
          unset($class_fwd[1]);
        }

        if (count($class_fwd) == 2) {
          list($class, $fwd) = $class_fwd;
        }

        if ($class == $prev_object->_class && !$fwd) {
          $ancestors[] = $prev_object;
        }
        elseif ($fwd) {
          $object = $prev_object->loadFwdRef($fwd, true);
          $ancestors[] = $object;
          $prev_object = $object;
        }
      }

      $ancestors = array_reverse($ancestors);

      foreach ($ancestors as $_ancestor) {
        $_config = self::getSelfConfig($_ancestor->_class, $_ancestor->_id, $config_keys);

        $configs[] = array(
          "object"        => $_ancestor,
          "config"        => $_config,
          "config_parent" => $parent_config,
        );

        $parent_config = array_merge($parent_config, $_config);
      }
    }

    return $configs;
  }

  /**
   * Change a particular configuration value
   *
   * @param string    $feature Feature
   * @param mixed     $value   Value
   * @param CMbObject $object  Host object
   *
   * @return null|string Store-like message
   */
  static function setConfig($feature, $value, CMbObject $object = null) {
    $where = array(
      "feature" => "= '$feature'",
    );

    if ($object) {
      $where["object_class"] = "= '$object->_class'";
      $where["object_id"]    = "= '$object->_id'";
    }
    else {
      $where["object_class"] = "IS NULL";
      $where["object_id"]    = "IS NULL";
    }

    $_config = new self;
    $_config->loadObject($where);

    $inherit = ($value === self::INHERIT);

    if ($_config->_id && $inherit) {
      return $_config->delete();
    }
    elseif (!$inherit) {
      if ($object) {
        $_config->setObject($object);
      }
      else {
        $_config->object_id    = null;
        $_config->object_class = null;
      }

      $_config->feature = $feature;
      $_config->value = $value;

      return $_config->store();
    }

    return null;
  }

  /**
   * Save the configuration values of an object
   *
   * @param array     $configs Configs
   * @param CMbObject $object  Object
   *
   * @return array A list of store messages if any error happens
   */
  static function setConfigs($configs, CMbObject $object = null) {
    $messages = array();

    foreach ($configs as $_feature => $_value) {
      if ($msg = self::setConfig($_feature, $_value, $object)) {
        $messages[] = $msg;
      }
    }

    return $messages;
  }

  /**
   * Change a particular configuration to inherit from its parent
   *
   * @param string    $feature Feature
   * @param CMbObject $object  Host object
   *
   * @return null|string Store-like message
   */
  static function inheritConfig($feature, CMbObject $object = null){
    return self::setConfig($feature, self::INHERIT, $object);
  }

  /**
   * Get a config's specs
   *
   * @param string $feature Space separated feature name
   *
   * @return null|array
   */
  static function getConfigSpec($feature) {
    $specs = self::getConfigsSpecs();

    if (!isset($specs[$feature])) {
      return null;
    }

    return $specs[$feature];
  }
}
