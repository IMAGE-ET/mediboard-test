<?php

/**
 * $Id$
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Utility class, used for external software data import
 */
class CExternalDBImport {
  /** @var CMySQLDataSource|CSQLDataSource */
  protected $_ds;

  /** @var string Mediboard class name */
  protected $_class;

  /** @var string External table name */
  protected $_table;

  /** @var string Primary key name */
  protected $_key;

  /** @var string SQL restriction */
  protected $_sql_restriction;

  /** @var array List of fields to select */
  protected $_select = array();

  /** @var array Field mapping between External DB => MB field */
  protected $_map = array();

  /** @var string Order by key name */
  protected $_order_by;

  /** @var CMbObject */
  public $_mb_object;

  /** @var int Stored object count */
  static $_count_stored = 0;

  protected $_import_tag_conf;
  protected $_import_function_name_conf;
  protected $_import_dsn;

  /** @var string External patient class name to import */
  static $_patient_class;

  /**
   * Get external software import tag
   *
   * @return string
   */
  function getImportTag() {
    return CAppUI::conf($this->_import_tag_conf);
  }

  /**
   * Get datasource
   *
   * @return CMySQLDataSource
   */
  function getDS() {
    if ($this->_ds) {
      return $this->_ds;
    }

    return $this->_ds = CSQLDataSource::get($this->_import_dsn);
  }

  /**
   * Get import function
   *
   * @return CFunctions
   */
  function getImportFunction() {
    static $function;

    if ($function) {
      return $function;
    }

    $function_name  = CAppUI::conf($this->_import_function_name_conf);
    $function       = new CFunctions;
    $function->text = $function_name;
    $function->loadMatchingObjectEsc();

    if (!$function->_id) {
      $function->group_id        = CGroups::loadCurrent()->_id;
      $function->type            = "cabinet";
      $function->compta_partagee = 0;
      $function->color           = "#CCCCCC";

      if ($msg = $function->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }

    return $function;
  }

  /**
   * @see parent::getSelectFields()
   */
  function getSelectFields() {
    return $this->_select;
  }

  /**
   * Get ORDER BY clause
   *
   * @return string|null
   */
  function getOrderBy() {
    $order_by = $this->_order_by;

    if (!$order_by) {
      return null;
    }

    return "DATE($order_by)";
  }

  /**
   * Get SELECT query
   *
   * @param string $where WHERE statement
   *
   * @return string
   */
  function getSelectQuery($where = null) {
    if (count($this->getSelectFields()) == 0) {
      $select = "$this->_table.*";
    }
    else {
      $items  = array();
      $fields = $this->getSelectFields();
      foreach ($fields as $col) {
        $items[] = "$this->_table.$col";
      }

      $select = implode(", ", $items);
    }

    $query = "SELECT $select";
    $query .= " FROM $this->_table";

    if ($this->_sql_restriction || $where) {
      $query .= " WHERE $this->_sql_restriction";

      if ($this->_sql_restriction && $where) {
        $query .= " AND ";
      }

      $query .= $where;
    }

    return $query;
  }

  static function importByClass($class, $start = null, $count = null, $reimport = false, $chir_id = null, $order = null, $date = null) {
    if (!is_subclass_of($class, "CExternalDBImport")) {
      CAppUI::stepAjax("Classe invalide", UI_MSG_ERROR);
    }

    /** @var self $self */
    $self = new static();
    $ds   = $self->getDS();

    /** @var self $object */
    $object = new $class;
    $query  = $object->getSelectQuery();

    $order_by = $object->getOrderBy();

    if ($date) {
      if ($object->_sql_restriction) {
        $query .= " AND ";
      }
      else {
        $query .= " WHERE ";
      }

      if ($order) {
        $date_min = CMbDT::date("-6 MONTH", $date);
        $date_max = $date;
      }
      else {
        $date_min = $date;
        $date_max = CMbDT::date("+6 MONTH", $date);
      }

      $query .= " $order_by BETWEEN '$date_min' AND '$date_max'";
    }

    if ($order && $order_by) {
      $query .= " ORDER BY $order_by DESC";
    }

    if (!$reimport) {
      $ids = array_flip($object->getDbIds());
    }

    $key_name  = $object->_key;
    $key_multi = strpos($key_name, "|") !== false;

    if ($key_multi) {
      $key_multi = explode("|", $key_name);
    }

    //echo $query;
    $res = $ds->exec($query);

    while ($count && ($hash = $ds->fetchAssoc($res, false))) {
      $oracle = $ds instanceof COracleDataSource;

      if (!$oracle) {
        $hash = array_change_key_case($hash, CASE_UPPER);
      }

      if ($key_multi) {
        $_values = array();
        foreach ($key_multi as $_col) {
          $_values[] = $hash[$_col];
        }
        $hash[$key_name] = implode("|", $_values);
      }

      if (!$reimport && isset($ids[$hash[$key_name]])) {
        continue;
      }

      if ($oracle) {
        $hash = $ds->readLOB($hash);
      }

      /** @var self $import_object */
      $import_object = new $class;

      $import_object->storeMbObject($hash);

      if (!$import_object->_mb_object || isset($import_object->_mb_object->_failed)) {
        continue;
      }

      $count--;
    }

    $ds->freeResult($res);

    return $date;
  }

  function loadList($query) {
    $ds   = self::getDS();
    $res  = $ds->exec($query);
    $list = array();

    while ($hash = $ds->fetchAssoc($res)) {
      $list[] = $hash;
    }

    $ds->freeResult($res);

    return $list;
  }

  /**
   * Returns an MBObject by its class and its Import ID
   *
   * @param string $class The Mediboard class name
   * @param string $db_id The Import ID
   *
   * @return CStoredObject The MB Object
   */
  function getMbObjectByClass($class, $db_id) {
    static $objects = array();
    $db_id = addslashes($db_id);

    if (isset($objects[$class][$db_id])) {
      return $objects[$class][$db_id];
    }

    $idex   = CIdSante400::getMatch($class, $this->getImportTag(), $db_id);
    $target = $idex->loadTargetObject();

    if ($idex->_id) {
      $objects[$class][$db_id] = $target;
    }

    return $target;
  }

  function getDbIds() {
    $request = new CRequest;
    $request->addColumn("DISTINCT id400");
    $request->addTable("id_sante400");
    $tag = $this->getImportTag();
    $request->addWhere(
      array(
        "object_class" => "= '$this->_class'",
        "tag"          => "= '$tag'",
      )
    );

    return CSQLDataSource::get("std")->loadColumn($request->makeSelect());
  }

  /**
   * Returns the CMbObject corresponding to the given $db_id
   *
   * @param string $db_id The Import ID
   *
   * @return CMbObject The CMbObject
   */
  function getMbObject($db_id) {
    return self::getMbObjectByClass($this->_class, $db_id);
  }

  /**
   * Store the external ID of the given object
   *
   * @param CMbObject $object The MB to store the external ID of
   * @param string    $db_id  The Import ID to store on the MB Object
   *
   * @return string The external ID store error message
   */
  function storeIdExt(CMbObject $object, $db_id) {
    $id_ext = new CIdSante400;
    $id_ext->setObject($object);
    $id_ext->tag   = $this->getImportTag();
    $id_ext->id400 = $db_id;
    $id_ext->escapeValues();
    $id_ext->loadMatchingObject();

    $id_ext->last_update = CMbDT::dateTime();
    $id_ext->unescapeValues();

    return $id_ext->store();
  }

  /**
   * @param string  $class Object class
   * @param integer $id    Object ID
   *
   * @return bool|CStoredObject The object
   */
  static function getOrImportObject($class, $id) {
    /** @var self $import_object */
    $import_object = new $class;
    $object        = self::getMbObjectByClass($import_object->_class, $id);

    if (!$object->_id) {
      $import_object->importObject($id);

      if (!$import_object->_mb_object || !$import_object->_mb_object->_id) {
        CAppUI::setMsg(CAppUI::tr($import_object->_class) . " non retrouvé et non importé : " . $id, UI_MSG_WARNING);

        return false;
      }

      $object = $import_object->_mb_object;
    }

    return $object;
  }

  /**
   * @param string $patient_id Import patient ID
   * @param string $prat       Import praticien ID
   * @param string $date       Date
   * @param string $idex       External ID
   *
   * @return bool|CSejour|CStoredObject Finds a sejour from a patient, praticien and date
   */
  function findSejour($patient_id, $prat, $date, $idex = null) {
    if ($idex) {
      $object = $this->getMbObjectByClass("CSejour", $idex);

      if ($object->_id) {
        return $object;
      }
    }

    // Trouver ou importer le patient
    $patient = $this->getOrImportObject(self::$_patient_class, $patient_id);
    if (!$patient || !$patient->_id) {
      CAppUI::setMsg("Patient non retrouvé et non importé : $patient_id", UI_MSG_WARNING);

      return false;
    }

    // Trouver le praticien du sejour
    $user = $this->getMbObjectByClass("CMediusers", $prat);
    if (!$user->_id) {
      CAppUI::setMsg("Praticien du séjour non retrouvé : $prat", UI_MSG_WARNING);

      return false;
    }

    // Recherche d'un séjour dont le debut peut
    // commencer 1 jour apres la date ou finir 2 jours avant
    $date = CMbDT::date($date);

    $sejour = new CSejour;
    $where  = array(
      "patient_id"   => "= '$patient->_id'",
      "praticien_id" => "= '$user->_id'",
      "DATE_SUB(`sejour`.`entree`, INTERVAL 1 DAY) < '$date'",
      "DATE_ADD(`sejour`.`sortie`, INTERVAL 2 DAY) > '$date'",
    );
    $sejour->loadObject($where);

    if ($sejour->_id && $idex) {
      $this->storeIdExt($sejour, $idex);
    }

    if (!$sejour->_id) {
      CAppUI::setMsg("Séjour non trouvé : $patient_id / $prat / $date", UI_MSG_WARNING);

      return false;
    }

    return $sejour;
  }

  static function findConsult($patient_id, $prat, $date) {
    // Trouver ou importer le patient
    $patient = self::getOrImportObject(self::$_patient_class, $patient_id);

    if (!$patient || !$patient->_id) {
      CAppUI::setMsg("Patient non retrouvé et non importé : $patient_id", UI_MSG_WARNING);

      return false;
    }

    // Trouver le praticien de la consult
    $mediuser = self::getMbObjectByClass("CMediusers", $prat);
    if (!$mediuser->_id) {
      CAppUI::setMsg("Praticien de la consult non retrouvé : $prat", UI_MSG_WARNING);

      return false;
    }

    // Recherche d'une consult qui se passe entre 2 jours avant ou 1 jour apres
    $date_min = CMbDT::date("-2 DAYS", $date);
    $date_max = CMbDT::date("+1 DAYS", $date);

    $consult = new CConsultation();

    $ljoin = array(
      "plageconsult" => "consultation.plageconsult_id = plageconsult.plageconsult_id",
    );

    $where = array(
      "consultation.patient_id" => "= '$patient->_id'",
      "plageconsult.chir_id"    => "= '$mediuser->_id'",
      "plageconsult.date"       => "BETWEEN '$date_min' AND '$date_max'",
    );

    $consult->loadObject($where, null, null, $ljoin);
    if (!$consult->_id) {
      $consult = self::makeConsult($patient->_id, $mediuser->_id, $date);
    }

    return $consult;
  }

  static function makeConsult($patient_id, $chir_id, $date) {
    $consult = new CConsultation;
    $date    = CMbDT::date($date);

    $plage = new CPlageconsult;
    $where = array(
      "plageconsult.chir_id" => "= '$chir_id'",
      "plageconsult.date"    => "= '$date'",
    );

    $plage->loadObject($where);

    if (!$plage->_id) {
      $plage->date    = $date;
      $plage->chir_id = $chir_id;
      $plage->debut   = "09:00:00";
      $plage->fin     = "19:00:00";
      $plage->freq    = "00:30:00";
      $plage->libelle = "Importation";

      if ($msg = $plage->store()) {
        return $msg;
      }
    }

    $consult->patient_id      = $patient_id;
    $consult->plageconsult_id = $plage->_id;
    $consult->heure           = "09:00:00"; // FIXME
    $consult->chrono          = ($date < CMbDT::date() ? CConsultation::TERMINE : CConsultation::PLANIFIE);

    if ($msg = $consult->store()) {
      return $msg;
    }

    if (!$consult->_id) {
      CAppUI::setMsg("Consultation non trouvée et non importée : $patient_id / $chir_id / $date", UI_MSG_WARNING);

      return false;
    }

    return $consult;
  }

  /**
   * Convert an external DB value to MB value
   *
   * @param array     $hash   The associative array containing all the data
   * @param string    $from   External DB field name
   * @param CMbObject $object The MB Object to get its specs
   *
   * @return string The value
   */
  function convertValue($hash, $from, $object) {
    $to  = $this->_map[$from];
    $src = $this->convertEncoding($hash[$from]);

    if (is_array($to)) {
      return CValue::read($to[1], $src, CValue::read($to, 2));
    }
    else {
      $v    = $src;
      $spec = $object->_specs[$to];

      switch (true) {
        case $spec instanceof CDateSpec:
        case $spec instanceof CBirthDateSpec:
          return reset(explode(" ", $v));

        case $spec instanceof CTimeSpec:
          return end(explode(" ", $v));

        case $spec instanceof CNumSpec:
        case $spec instanceof CNumcharSpec:
        case $spec instanceof CPhoneSpec:
          return preg_replace("/[^0-9]/", "", $v);
      }

      return $v;
    }
  }

  function convertEncoding($string) {
    return $string;
  }

  /**
   * Bind a hash to $object
   *
   * @param array     $hash   The hash to bind to the CMbObject
   * @param CMbObject $object The CMbObject
   *
   * @return void
   */
  function bindObject($hash, CMbObject $object) {
    foreach ($this->_map as $from => $to) {
      if (is_array($to)) {
        $to = reset($to);
      }

      $value         = $this->convertValue($hash, $from, $object);
      $object->{$to} = $value;
    }
  }

  /**
   * Get a hash from the primary key
   *
   * @param string $id The primary key value
   *
   * @return array The hash
   */
  protected function getHash($id) {
    $id = $this->getDS()->escape($id);

    $sep          = "|";
    $key          = $this->_key;
    $key_multi    = strpos($key, $sep) !== false;
    $values_multi = "";

    if (!$key_multi) {
      $where = "$key = '$id'";
    }
    else {
      $cols         = array_combine(explode($sep, $key), explode($sep, $id));
      $values_multi = implode("|", $cols);
      $where        = array();

      foreach ($cols as $_col => $_value) {
        $where[] = "$_col = '$_value'";
      }
      $where = implode(" AND ", $where);
    }

    $query = $this->getSelectQuery($where);
    $hash  = $this->_ds->loadHash($query);

    if ($key_multi) {
      $hash[$key] = $values_multi;
    }

    if ($hash == false) {
      return $hash;
    }

    return $hash;
  }

  /**
   * Bind a DB entry to a CMbObject from the primary key
   *
   * @param string $id The Import ID
   *
   * @return CMbObject The CMbObject
   */
  function mapIdToMbObject($id) {
    return $this->mapHashToMbObject($this->getHash($id));
  }

  /**
   * Bind a hash to a new CMbObject
   *
   * @param array $hash     The associative array
   * @param       CMbObject ,string $object The object or the class name
   *
   * @return CMbObject The CMbObject
   */
  function mapHashToMbObject($hash, $object = null) {
    if ($object) {
      $this->_mb_object = $object;
    }
    else {
      $this->_mb_object = new $this->_class;
    }

    $this->bindObject($hash, $this->_mb_object);

    return $this->_mb_object;
  }

  /**
   * Stores a CMbObject from a hash
   *
   * @param array   $hash  The associative array
   * @param boolean $force Force the object re-importation
   *
   * @return string The store message
   */
  function storeMbObject($hash, $force = false) {
    $db_id  = $this->getId($hash);
    $object = $this->getMbObject($db_id);

    // If object was already imported
    if (!$force && $object->_id) {
      return null;
    }

    $this->mapHashToMbObject($hash, $object);

    if (isset($this->_mb_object->_failed)) {
      return;
    }

    $this->_mb_object->repair();

    if ($msg = $this->_mb_object->store()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);

      return $msg;
    }
    else {
      CAppUI::setMsg("{$this->_mb_object->_class}-msg-create");
    }

    self::$_count_stored++;

    return self::storeIdExt($this->_mb_object, $this->getId($hash));
  }

  function importObject($db_id, $force = false) {
    $hash = $this->getHash($db_id);

    if (empty($hash)) {
      CAppUI::setMsg("ID <strong>$db_id</strong> inconnu", UI_MSG_ALERT);

      return;
    }

    $this->storeMbObject($hash, $force);
  }

  function getId($hash) {
    return $hash[$this->_key];
  }

  protected function extractPhone($string) {
    return substr(preg_replace("/[^0-9]/", "", $string), 0, 10);
  }
}
