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

class CExClassField extends CExListItemsOwner {
  public $ex_class_field_id;

  public $ex_group_id;
  public $subgroup_id;
  public $name; // != object_class, object_id, ex_ClassName_event_id,
  public $prop;
  public $disabled;

  //public $report_level;
  public $report_class;
  public $concept_id;
  public $predicate_id;
  public $prefix;
  public $suffix;
  public $show_label;
  public $tab_index;

  public $formula;
  public $_formula;
  public $result_in_title;

  public $coord_field_x;
  public $coord_field_y;
  //public $coord_field_colspan;
  //public $coord_field_rowspan;

  public $coord_label_x;
  public $coord_label_y;

  // Pixel positionned
  public $coord_left;
  public $coord_top;
  public $coord_width;
  public $coord_height;

  public $_locale;
  public $_locale_desc;
  public $_locale_court;

  public $_triggered_data;

  /** @var CExClassFieldGroup */
  public $_ref_ex_group;

  /** @var CExClass */
  public $_ref_ex_class;

  /** @var CExClassFieldTranslation[] */
  public $_ref_translation;

  /** @var CExClassFieldPredicate[] */
  public $_ref_predicates;

  /** @var CExClassFieldProperty[] */
  public $_ref_properties;

  /** @var CExClassFieldPredicate */
  public $_ref_predicate;

  /** @var CExConcept */
  public $_ref_concept;

  /** @var CMbFieldSpec */
  public $_spec_object;

  public $_ex_class_id;
  public $_default_properties;
  public $_no_size = false;
  public $_make_unique_name = true;

  public $_dont_drop_column;

  static $_load_lite = false;

  static $_indexed_types = array(
    "ref",
    "date",
    "dateTime",
    "time",
  );

  static $_data_type_groups = array(
    array("ipAddress"),
    array("bool"),
    array("enum"),
    array("ref"),
    array("num", "numchar"),
    array("pct", "float", "currency"),
    array("time"),
    array("date", "birthDate"),
    array("dateTime"),
    array("code"),
    array("email"),
    array("password", "str"),
    array("php", "xml", "html", "text"),
  );

  static $_property_fields_all = array(
    "prefix",
    "suffix",
    "tab_index",
    "show_label",
    "coord_left",
    "coord_top",
    "coord_width",
    "coord_height",
  );

  static $_property_fields = array(
    "prefix",
    "suffix",
    "tab_index",
  );

  static $_formula_token_re = "/\[([^\]]+)\]/";

  static $_formula_valid_types = array(
    "float", "num", "numchar", "pct", "currency"/*, "date", "dateTime", "time"*/
  );

  static $_concat_valid_types  = array(
    "float", "num", "numchar", "pct", "currency", /*"date", "dateTime", "time",*/
    "str", "text", "code", "email"
  );

  static $_formula_constants = array(
    "DateCourante",
    "HeureCourante",
    "DateHeureCourante"
  );

  static $_formula_intervals = array(
    "Min" => "Minutes",
    "H"   => "Heures",
    "J"   => "Jours",
    "Sem" => "Semaines",
    "M"   => "Mois",
    "A"   => "Années",
  );

  static $_prop_escape = array(
    " " => "\\x20",
    "|" => "\\x7C",
  );

  // types pouvant être utilisés pour des calculs / concaténation
  static function formulaCanArithmetic($type) {
    return in_array($type, self::$_formula_valid_types) ||
      $type == "enum" ||
      $type == "date" ||
      $type == "datetime" || $type == "dateTime" ||
      $type == "time";
  }
  static function formulaCanConcat($type) {
    return in_array($type, self::$_concat_valid_types);
  }
  static function formulaCan($type) {
    return self::formulaCanConcat($type) || self::formulaCanArithmetic($type);
  }

  // types pouvant herberger des resultats
  static function formulaCanResultArithmetic($type) {
    return in_array($type, self::$_formula_valid_types);
  }
  static function formulaCanResultConcat($type) {
    return $type === "text";
  }
  static function formulaCanResult($type) {
    return self::formulaCanResultConcat($type) || self::formulaCanResultArithmetic($type);
  }

  static function getTypes(){
    $types = array(
      "enum",
      "set",
      "str",
      "text",
      "bool",
      "num",
      "float",
      "date",
      "time",
      "dateTime",
      "pct",
      "phone",
      "birthDate",
      "currency",
      "email",
    );

    return array_intersect_key(CMbFieldSpecFact::$classes, array_flip($types));
  }

  /**
   * @return array
   */
  function getPropertyFields(){
    if ($this->_id && $this->loadRefExClass()->pixel_positionning) {
      return self::$_property_fields_all;
    }

    return self::$_property_fields;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field";
    $spec->key   = "ex_class_field_id";
    $spec->uniques["name"] = array("ex_group_id", "name");

    // should ignore empty values
    //$spec->uniques["coord_label"] = array("ex_group_id", "coord_label_x", "coord_label_y");
    //$spec->uniques["coord_field"] = array("ex_group_id", "coord_field_x", "coord_field_y");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["ex_group_id"] = "ref class|CExClassFieldGroup cascade";
    $props["subgroup_id"] = "ref class|CExClassFieldSubgroup nullify";
    $props["concept_id"]  = "ref class|CExConcept autocomplete|name";
    $props["name"]        = "str notNull protected canonical";
    $props["disabled"]    = "bool notNull default|0";
    //$props["report_level"]= "enum list|1|2|host";
    $props["report_class"]= "enum list|".implode("|", CExClassEvent::getReportableClasses());
    $props["prop"]        = "text notNull";
    $props["predicate_id"]= "ref class|CExClassFieldPredicate autocomplete|_view|true nullify";

    $props["prefix"]      = "str";
    $props["suffix"]      = "str";
    $props["show_label"]  = "bool notNull default|1";
    $props["tab_index"]   = "num";

    $props["formula"]     = "text"; // canonical tokens
    $props["_formula"]    = "text"; // localized tokens
    $props["result_in_title"] = "bool notNull default|0";

    $props["coord_field_x"] = "num min|0 max|100";
    $props["coord_field_y"] = "num min|0 max|100";
    //$props["coord_field_colspan"] = "num min|1 max|100";
    //$props["coord_field_rowspan"] = "num min|1 max|100";

    $props["coord_label_x"] = "num min|0 max|100";
    $props["coord_label_y"] = "num min|0 max|100";

    // Pixel positionned
    $props["coord_left"]   = "num";
    $props["coord_top"]    = "num";
    $props["coord_width"]  = "num min|1";
    $props["coord_height"] = "num min|1";

    $props["_ex_class_id"]  = "ref class|CExClass";

    $props["_locale"]     = "str notNull";
    $props["_locale_desc"]  = "str";
    $props["_locale_court"] = "str";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["enum_translations"]  = "CExClassFieldEnumTranslation ex_class_field_id";
    $backProps["field_translations"] = "CExClassFieldTranslation ex_class_field_id";
    $backProps["list_items"]         = "CExListItem field_id";
    $backProps["ex_triggers"]        = "CExClassFieldTrigger ex_class_field_id";
    $backProps["properties"]         = "CExClassFieldProperty object_id";
    $backProps["predicates"]         = "CExClassFieldPredicate ex_class_field_id";
    $backProps["identifiants"]       = "CIdSante400 object_id cascade";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = "$this->name [$this->prop]";

    $this->formulaFromDB();

    if (!$this->coord_width && !$this->coord_height) {
      $this->_no_size = true;
    }

    if (!self::$_load_lite) {
      $this->_ex_class_id = $this->loadRefExGroup()->ex_class_id;

      // must be called in the class editor
      if (!CExObject::$_locales_cache_enabled) {
        $this->updateTranslation();
      }
    }
  }

  /**
   * Get default styling
   *
   * @param bool $cache Cache results
   *
   * @return array
   */
  function getDefaultProperties($cache = true){
    if ($cache && $this->_default_properties !== null) {
      return $this->_default_properties;
    }

    return $this->_default_properties = CExClassFieldProperty::getDefaultPropertiesFor($this);
  }

  /**
   * @see parent::getAutocompleteList()
   */
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin = null, $order = null) {
    $list = $this->loadList($where, null, null, null, $ljoin);

    $real_list = array();
    $re = preg_quote($keywords);
    $re = CMbString::allowDiacriticsInRegexp($re);
    $re = str_replace("/", "\\/", $re);
    $re = "/($re)/i";

    foreach ($list as $_ex_field) {
      if ($keywords == "%" || $keywords == "" || preg_match($re, $_ex_field->_view)) {
        $_ex_field->updateTranslation();
        $_group = $_ex_field->loadRefExGroup();
        $_ex_field->_view = "$_group->_view - $_ex_field->_view";

        $real_list[$_ex_field->_id] = $_ex_field;
      }
    }

    return $real_list;
  }

  /**
   * Get all the fields' names
   *
   * @param bool $name_as_key Put the names in the keys
   * @param bool $all_groups  Load all groups
   *
   * @return string[] List of field names
   */
  function getFieldNames($name_as_key = true, $all_groups = true){
    $ds = $this->_spec->ds;

    $req = new CRequest();
    $req->addTable($this->_spec->table);
    $req->addSelect("ex_class_field.name, ex_class_field_translation.std AS locale");

    $ljoin = array(
      "ex_class_field_translation" => "ex_class_field_translation.ex_class_field_id = ex_class_field.ex_class_field_id"
    );
    $req->addLJoin($ljoin);

    $this->completeField("ex_group_id");

    $where = array();
    if ($all_groups) {
      $ex_group = $this->loadRefExGroup();
      $where_ids = array(
        "ex_class_id" => $ds->prepare("= %", $ex_group->ex_class_id),
      );
      $ids = $ex_group->loadIds($where_ids);
      $where["ex_group_id"] = $ds->prepareIn($ids);
    }
    else {
      $where["ex_group_id"] = $ds->prepare("= %", $this->ex_group_id);
    }
    $req->addWhere($where);

    $results = $ds->loadList($req->getRequest());

    if ($name_as_key) {
      return array_combine(CMbArray::pluck($results, "name"), CMbArray::pluck($results, "locale"));
    }

    return array_combine(CMbArray::pluck($results, "locale"), CMbArray::pluck($results, "name"));
  }

  /**
   * Get formula values
   *
   * @return string[]
   */
  function getFormulaValues(){
    $ret = true;

    if ($this->concept_id) {
      $concept = $this->loadRefConcept();
      if (!$concept->ex_list_id) {
        return $ret;
      }

      $list = $concept->loadRefExList();
      if (!$list->coded) {
        return $ret;
      }

      $items = $list->loadRefItems(true);
      $ret = array_combine(CMbArray::pluck($items, "ex_list_item_id"), CMbArray::pluck($items, "code"));
    }

    return $ret;
  }

  function validateFormula($formula) {

  }

  /**
   * @param bool $update
   *
   * @return string|null
   */
  function formulaToDB($update = true) {
    if ($this->_formula === null) {
      return null;
    }

    if ($this->_formula === "") {
      $this->formula = "";
      return null;
    }

    $field_names = $this->getFieldNames(false);
    $formula = $this->_formula;

    if (/*!in_array($formula, self::$_formula_constants) && */!preg_match_all(self::$_formula_token_re, $formula, $matches)) {
      return "Formule invalide";
    }

    $msg = array();

    //if (isset($matches)) {
      foreach ($matches[1] as $_match) {
        $_trimmed = trim($_match);
        if (!array_key_exists($_trimmed, $field_names)) {
          $msg[] = "\"$_match\"";
        }
        else {
          $formula = str_replace("[$_match]", "[".$field_names[$_trimmed]."]", $formula);
        }
      }
    //}

    if (empty($msg)) {
      if ($update) {
        $this->formula = $formula;
      }
      return null;
    }

    return "Des éléments n'ont pas été reconnus dans la formule: ".implode(", ", $msg);
  }

  function formulaFromDB(){
    //$this->completeField("formula"); memory limit :(

    if (!$this->formula) {
      return;
    }

    $field_names = $this->getFieldNames(true);

    $formula = $this->formula;

    if (/*!in_array($formula, self::$_formula_constants) && */!preg_match_all(self::$_formula_token_re, $formula, $matches)) {
      return "Formule invalide";
    }

    //if (isset($matches)) {
      foreach ($matches[1] as $_match) {
        $_trimmed = trim($_match);
        if (array_key_exists($_trimmed, $field_names)) {
          $formula = str_replace($_match, $field_names[$_trimmed], $formula);
        }
      }
    //}

    $this->_formula = $formula;
  }

  /**
   * @see parent::check()
   */
  function checkFormula(){
    return $this->formulaToDB(false);
  }

  /**
   * @see parent::check()
   */
  function check(){
    if ($msg = $this->checkFormula(false)) {
      return $msg;
    }

    // verification des coordonnées
    /*$where = array(
      $this->_spec->key => "!= '$this->_id'",
      "coord_field_x" => "NOT BETWEEN coord_field_x AND coord_field_x + coord_field_colspan",
      "coord_field_y" => "NOT BETWEEN coord_field_y AND coord_field_y + coord_field_rowspan",
    );*/

    $this->formulaToDB(true);

    return parent::check();
  }

  /**
   *
   */
  function loadTriggeredData(){
    $triggers = $this->loadBackRefs("ex_triggers");

    $this->_triggered_data = array();

    if (!count($triggers)) {
      return;
    }

    $keys   = CMbArray::pluck($triggers, "trigger_value");
    $values = CMbArray::pluck($triggers, "ex_class_triggered_id");

    $this->_triggered_data = array_combine($keys, $values);
  }

  /**
   * @return CExClassFieldPredicate[]
   */
  function loadRefPredicates(){
    return $this->_ref_predicates = $this->loadBackRefs("predicates");
  }

  /**
   * @return CExClassFieldProperty[]
   */
  function loadRefProperties(){
    return $this->_ref_properties = $this->loadBackRefs("properties");
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldPredicate
   */
  function loadRefPredicate($cache = true){
    return $this->_ref_predicate = $this->loadFwdRef("predicate_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldGroup
   */
  function loadRefExGroup($cache = true){
    if ($cache && $this->_ref_ex_group && $this->_ref_ex_group->_id) {
      return $this->_ref_ex_group;
    }

    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }

  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadRefExGroup($cache)->loadRefExClass($cache);
  }

  /**
   * @param bool $cache [optional]
   *
   * @return CExConcept
   */
  function loadRefConcept($cache = true){
    return $this->_ref_concept = $this->loadFwdRef("concept_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldTranslation
   */
  function loadRefTranslation($cache = true) {
    if ($cache && $this->_ref_translation) {
      return $this->_ref_translation;
    }

    $trans = CExClassFieldTranslation::tr($this->_id);
    $trans->fillIfEmpty($this->name);
    return $this->_ref_translation = $trans;
  }

  /**
   * @return CExClassFieldEnumTranslation[]
   */
  function loadRefEnumTranslations() {
    $trans = new CExClassFieldEnumTranslation;
    $trans->lang = CAppUI::pref("LOCALE");
    $trans->ex_class_field_id = $this->_id;
    return $trans->loadMatchingList();
  }

  /**
   * @return void
   */
  function updateTranslation(){
    $items = $this->getRealListOwner()->getItemNames();

    $ex_class = $this->loadRefExClass();

    $key = $ex_class->getExClassName().".$this->name";

    global $locales;
    $locales["{$key}."] = CAppUI::tr("Undefined");

    foreach ($items as $_id => $_item) {
      $locales["$key.$_id"] = $_item;
    }

    $trans = null;

    $local_key = "$key-$this->name";
    if (isset($locales[$local_key])) {
      $this->_locale = $locales[$local_key];
    }
    else {
      $trans = $trans ? $trans : $this->loadRefTranslation();
      $this->_locale = $trans->std;
    }

    $local_key = "$key-$this->name-desc";
    if (isset($locales[$local_key])) {
      $this->_locale_desc = $locales[$local_key];
    }
    else {
      $trans = $trans ? $trans : $this->loadRefTranslation();
      $this->_locale_desc = $trans->desc;
    }

    $local_key = "$key-$this->name-court";
    if (isset($locales[$local_key])) {
      $this->_locale_court = $locales[$local_key];
    }
    else {
      $trans = $trans ? $trans : $this->loadRefTranslation();
      $this->_locale_court = $trans->court;
    }

    $this->_view = $this->_locale;
  }

  function getTableName(){
    return $this->loadRefExClass()->getTableName();
  }

  /**
   * @return CMbFieldSpec
   */
  function getSpecObject(){
    CBoolSpec::$_default_no = false;
    $this->_spec_object = @CMbFieldSpecFact::getSpecWithClassName("CExObject", $this->name, $this->prop);
    CBoolSpec::$_default_no = true;

    return $this->_spec_object;
  }

  function getSQLSpec($union = true){
    $spec_obj = $this->getSpecObject();
    $db_spec = $spec_obj->getFullDBSpec();

    if ($union) {
      $ds = $this->_spec->ds;
      $db_parsed = CMbFieldSpec::parseDBSpec($db_spec, true);

      if ($db_parsed['type'] === "ENUM") {
        $prop_parsed = $ds->getDBstruct($this->getTableName(), $this->name, true);

        if (isset($prop_parsed[$this->name])) {
          $db_parsed['params'] = array_merge($db_parsed['params'], $prop_parsed['params']);
        }

        $db_parsed['params'] = array_unique($db_parsed['params']);

        $spec_obj->list = implode("|", $db_parsed['params']);
        $db_spec = $spec_obj->getFullDBSpec();
      }
    }

    return $db_spec;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields(){
    $reset_position = $this->fieldModified("ex_group_id") || $this->fieldModified("disabled");

    // If we change its group, we need to reset its coordinates
    if ($reset_position) {
      $this->coord_field_x = "";
      $this->coord_field_y = "";
      $this->coord_label_x = "";
      $this->coord_label_y = "";
      $this->subgroup_id = "";
    }

    $subgroup_modified = $this->fieldModified("subgroup_id");
    if ($reset_position || $subgroup_modified) {
      if (!$this->fieldModified("coord_left")) {
        $this->coord_left = "";
      }

      if (!$this->fieldModified("coord_top")) {
        $this->coord_top = "";
      }
    }

    /*if ($group_modified || $coord_modified) {
      $this->coord_field_colspan = "";
      $this->coord_field_rowspan = "";
    }*/

    parent::updatePlainFields();
  }

  static function getUniqueName(){
    $sibling = new self;

    do {
      $uniqid = uniqid("f");
      $where = array(
        "name" => "= '$uniqid'",
      );
      $sibling->loadObject($where);
    } while ($sibling->_id);

    return $uniqid;
  }

  /**
   * @see parent::store()
   */
  function store(){
    if (!$this->_id && $this->concept_id) {
      $this->prop = $this->loadRefConcept()->prop;
    }

    // pour la valeur par defaut des enums
    if ($this->prop !== null) {
      $this->prop = str_replace("\\", "\\\\", $this->prop);
    }

    if (!$this->_id && $this->_make_unique_name) {
      $this->name = self::getUniqueName();
    }

    if ($msg = $this->check()) {
      return $msg;
    }

    /*if (!preg_match('/^[a-z0-9_]+$/i', $this->name)) {
      return "Nom de champ invalide ($this->name)";
    }*/

    $ds = $this->_spec->ds;

    if (!$this->_id) {
      $table_name = $this->getTableName();
      $sql_spec = $this->getSQLSpec(false);
      $query = "ALTER TABLE `$table_name` ADD `$this->name` $sql_spec";

      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu être ajouté à la table '$table_name' (".$ds->error().")";
      }

      $spec_type = $this->_spec_object->getSpecType();

      // ajout de l'index
      if (in_array($spec_type, self::$_indexed_types)) {
        $query = "ALTER TABLE `$table_name` ADD INDEX (`$this->name`)";
        $ds->query($query);
      }
    }
    else if ($this->fieldModified("name") || $this->fieldModified("prop")) {
      $table_name = $this->getTableName();
      $sql_spec = $this->getSQLSpec();
      $query = "ALTER TABLE `$table_name` CHANGE `{$this->_old->name}` `$this->name` $sql_spec";

      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu être mis à jour (".$ds->error().")";
      }
    }

    $locale       = $this->_locale;
    $locale_desc  = $this->_locale_desc;
    $locale_court = $this->_locale_court;
    $triggered_data = $this->_triggered_data;

    if ($msg = parent::store()) {
      return $msg;
    }

    // form triggers
    if ($triggered_data) {
      $triggered_object = json_decode($triggered_data, true);

      if (is_array($triggered_object)) {
        foreach ($triggered_object as $_value => $_class_trigger_id) {
          $trigger = new CExClassFieldTrigger();
          $trigger->ex_class_field_id = $this->_id;
          $trigger->trigger_value = $_value;
          $trigger->loadMatchingObject();

          if ($_class_trigger_id) {
            $trigger->ex_class_triggered_id = $_class_trigger_id;
            $trigger->store();
          }
          else {
            $trigger->delete();
          }
        }
      }
    }

    // self translations
    if ($locale || $locale_desc || $locale_court) {
      $trans = $this->loadRefTranslation();
      $trans->std = $locale;
      $trans->desc = $locale_desc;
      $trans->court = $locale_court;
      if ($msg = $trans->store()) {
        mbTrace($msg, get_class($this), true);
      }
    }

    return null;
  }

  /**
   * @see parent::delete()
   */
  function delete(){
    return "Les champs ne peuvent pas être supprimés, veuillez plutôt le désactiver.";
    /*
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }

    if (!$this->_dont_drop_column) {
      $this->completeField("name");

      $table_name = $this->loadRefExClass()->getTableName();
      $query = "ALTER TABLE `$table_name` DROP `$this->name`";
      $ds = $this->_spec->ds;

      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu être supprimé (".$ds->error().")";
      }
    }

    return parent::delete();*/
  }

  /**
   * @return CExListItemsOwner
   */
  function getRealListOwner(){
    if ($this->concept_id) {
      return $this->loadRefConcept()->getRealListOwner();
    }

    return parent::getRealListOwner();
  }

  /**
   * @param $str
   *
   * @return string
   */
  static function escapeProp($str) {
    return strtr($str, self::$_prop_escape);
  }

  /**
   * @param $str
   *
   * @return string
   */
  static function unescapeProp($str) {
    return strtr($str, array_flip(self::$_prop_escape));
  }
}
