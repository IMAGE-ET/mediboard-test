<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMouvement400 extends CRecordSante400 {
  const STATUS_ETABLISSEMENT = 0;
  const STATUS_FONCSALLSERV  = 1;
  const STATUS_PRATICIEN     = 2;
  const STATUS_PATIENT       = 3;
  const STATUS_SEJOUR        = 4;
  const STATUS_OPERATION     = 5;
  const STATUS_ACTES         = 6;
  const STATUS_NAISSANCE     = 7;
  
  public $base;
  public $table;
  public $class;
  public $origin;
  
  public $mark_field;
  public $type_field;
  public $when_field;
  public $trigger_key_field;
  public $origin_key_field;
  
  public $old_prefix;
  public $new_prefix;
  public $origin_prefix;
  
  public $statuses = array(null, null, null, null, null, null, null, null);
  public $cached   = array(null, null, null, null, null, null, null, null);
  
  public $status = null;
  public $rec = null;
  public $type = null;
  public $prod = null;
  public $when = null;
  public $mark = null;
  
  public $changedFields = array();

  function __construct() {
    parent::__construct();
    $this->class = get_class($this); 
  }

  function initialize() {
    // Consume metadata
    $this->rec  = $this->consume($this->trigger_key_field);
    $this->type = $this->consume($this->type_field);

    if ($this->when_field) {
      $this->when = $this->consume($this->when_field);
    }
 
    // Analyse changed fields
    foreach (array_keys($this->data) as $beforeName) {
      $matches = array();
      if (!preg_match("/$this->old_prefix(\w*)/i", $beforeName, $matches)) {
        continue;
      }
      
      $name = $matches[1];
      $afterName = $this->new_prefix . $name;
      if ($this->data[$beforeName] != $this->data[$afterName]) {
        $this->changedFields[] = $name;
      }
    }
  }
  
  
  /**
   * Try and store trigger mark according to module config
   * 
   * @param string $mark Mark content
   *
   * @return void
   */
  static function storeMark($mark) {
    if (CAppUI::conf("dPsante400 mark_row")) {
      if ($msg = $mark->store()) {
         trigger_error("Enable to store CTriggerMark: $msg", E_USER_WARNING);
      }
    }
  }
  
  /**
   * Mark mouvement as being handled
   * 
   * @return void
   */
  function checkOut() {
    return;
    $mark = $this->loadTriggerMark();
    $mark->mark = "========";
    $mark->done = "0";
    self::storeMark($mark);
  }
  
  /**
   * Load List of mouvements
   * 
   * @param bool $marked Will load only marked (already processed)
   * @param int  $max    Maximum number of mouvements
   * 
   * @return array|CMouvement400
   */
  function loadList($marked = false, $max = 100) {
    $query = "SELECT * FROM $this->base.$this->table";
    $query.= $this->getNewMarkedClause($marked, $max);
    $query.= $this->getFilterClause();
    $query.= "\n ORDER BY $this->trigger_key_field";
 
    $mouvs = CRecordSante400::loadMultiple($query, array(), $max, get_class($this));

    // Multiple checkout
    foreach ($mouvs as &$mouv) {
      $mouv->initialize(); 
      $mouv->checkOut();     
    }
            
    return $mouvs;
  }
  
  /**
   * Mark obsolete triggers
   * 
   * @param string  $newest
   * @param integer $max [optional]
   *
   * @return integer Number of marks, store-like message on error
   */
  function markObsoleteTriggers($newest, $max = 100) {
    $mark = new CTriggerMark();
    $mark->trigger_class = get_class($this);
    $where["trigger_class"] = "= '$mark->trigger_class'";
    $where["done"] = "= '0'";
    $where["mark"] = "!= '========'";
    $where["trigger_number"] = "<= '$newest'";

    /** @var CTriggerMark[] $marks */
    $marks = $mark->loadList($where, null, $max);
    foreach ($marks as $_mark) {
      $_mark->done = "1";    
      $_mark->mark = "obsolete"; 
      if ($msg = $_mark->store()) {
        return $msg;
      }  
    }
    
    return count($marks);
  }
  
  /**
   * Get a filter where clause
   * 
   * @return string SQL where clause
   */
  function getFilterClause() {
    return;
  }
  
  /**
   * Get a marked where clause
   * 
   * @return string SQL where clause
   */
  function getNewMarkedClause($marked, $max = 100) {
    $mark = new CTriggerMark();
    $mark->trigger_class = get_class($this);

    if ($marked) {
      $where["trigger_class"] = "= '$mark->trigger_class'";
      $where["done"] = "= '0'";
      $where["mark"] = "!= '========'";
      $marks = $mark->loadList($where, null, $max);
      $clause = "\n WHERE $this->trigger_key_field " . CSQLDataSource::prepareIn(CMbArray::pluck($marks, "trigger_number"));
    }
    else {
      $mark->loadMatchingObject("trigger_number DESC");
      $last_number = $mark->trigger_number ? $mark->trigger_number : 0;
      $clause = "\n WHERE $this->trigger_key_field > '$last_number'";
    }
    return $clause;
  }
  
  /**
   * Count triggers
   * 
   * @param boolean $marked [optional] 
   * @param string  $oldest [optional]
   *
   * @return integer
   */
  function count($marked = false, $newest = null) {
    if ($marked) {
      $mark = new CTriggerMark();
      $mark->trigger_class = get_class($this);
      $where["trigger_class"] = "= '$mark->trigger_class'";
      $where["done"] = "= '0'";
      $where["mark"] = "!= '========'";
      
      if ($newest) {
        $where["trigger_number"] = "<= '$newest'";
      }
      
      $total = $mark->countList($where);
    }
    else {
      $record = new CRecordSante400();
      $query = "SELECT COUNT(*) AS TOTAL FROM $this->base.$this->table";
      $query.= $this->getNewMarkedClause($marked);
      $query.= $this->getFilterClause();

      if ($newest) {
        $query.= "AND $this->trigger_key_field <= '$newest'";
      }

      $record->query($query);
      $total = $record->consume("TOTAL");
    }
    
    return $total;
  }
  
  /**
   * Load latest with former marks
   * 
   * @param int $max Max rows
   *
   * @return array
   */
  function loadListWithFormerMark($max)  {
    $query = "SELECT * FROM $this->base.$this->table
      WHERE $this->mark_field NOT IN ('', 'OKOKOKOK')";
    $query.= $this->getFilterClause();
    $query.= "\n ORDER BY $this->trigger_key_field DESC";
    $mouvs = CRecordSante400::loadMultiple($query, array(), $max, get_class($this));
    foreach ($mouvs as &$mouv) {
      $mouv->initialize();
    }
        
    return $mouvs;
  }
  
  /**
   * Load latest succeded with former marks
   *
   * @return array
   */
  function loadLatestSuccessWithFormerMark() {
    $query = "SELECT * FROM $this->base.$this->table
      WHERE $this->mark_field = 'OKOKOKOK'";
    $query.= $this->getFilterClause();
    $query.= "\n ORDER BY $this->trigger_key_field DESC";
    $this->loadOne($query);
    $this->initialize();
  }

  /**
   * Load and checkout mouvement for given record index
   * 
   * @param string $rec Record index
   *
   * @param void
   */
  function load($rec) {
    $query = "SELECT * FROM $this->base.$this->table 
      WHERE $this->trigger_key_field = ?";

    $values = array (
      intval($rec),
    );    

    $this->loadOne($query, $values);
    $this->initialize();
    $this->checkOut();     
  }
  
  /**
   * Load oldest mouvement in the table
   * 
   * @return void
   */
  function loadOldest() {
    $query = "SELECT * FROM $this->base.$this->table 
      ORDER BY $this->trigger_key_field ASC";
    $this->loadOne($query);
    $this->initialize();
  }
  
  /**
   * Load latest mouvement in the table
   * 
   * @return void
   */
  function loadLatest() {
    $query = "SELECT * FROM $this->base.$this->table 
      ORDER BY $this->trigger_key_field DESC";
    $this->loadOne($query);
    $this->initialize();
  }
  
  /**
   * Mark the mouvement row
   * ======== : checked out, not used anymore, caused some everlocked marks
   * OKOKOKOK : processed
   * 124--*-- : each of 8 steps are done :
   *      n : times
   *      - : undone due to errors 
   *      * : skipped 
   */  
  function markRow() {
    // Get the final mark
    $this->status = "";
    foreach ($this->statuses as $status) {
      $char = "?";
      if (null === $status) $char = "-";
      if ("*"  === $status) $char = "*";
      if (is_int($status))  $char = chr($status + ord("0"));
      $this->status .= $status;
    }
    
    $mark = $this->loadTriggerMark();
    $mark->when = "now";
    $mark->mark = $this->status;
    $mark->done = in_array(null, $this->statuses, true) ? "0" : "1";
    self::storeMark($mark);
  }
  
  /**
   * Initialisation du status à zéro si non existant
   * 
   * @param int $rank
   * @param int $value valeur à mettre, incrémente le rang si null 
   */
  function setStatus($rank) {
    if (null === @$this->statuses[$rank]) {
      $this->statuses[$rank] = 0;
    }
  }
  
  /**
   * Changement de status sur un rang à une valeur donnée
   * 
   * @param int $rank
   * @param int $value valeur à mettre, incrémente le rang si null 
   */
  function markStatus($rank, $value = null) {
    $this->statuses[$rank] = null === $value ? @$this->statuses[$rank] + 1 : $value;
  }

  /**
   * Analogie à markStatus(), mais compte les objets récupérés du cache
   * 
   * @param int $rank
   * @param int $value valeur à mettre, incrémente le rang si null 
   */
    function markCache($rank, $value = null) {
    $this->markStatus($rank, $value);
    $this->cached[$rank] = null === $value ? @$this->cached[$rank] + 1 : $value;
  }
  
  /**
   * Cherche la mark pour ce mouvement et la complète
   * 
   * @return CTriggerMark
   */
  function loadTriggerMark() {
    $mark = new CTriggerMark();
    $mark->trigger_class = get_class($this);    
    $mark->trigger_number = $this->rec;
    
    // Recherche 
    if ($mark->trigger_number) {
      $mark->loadMatchingObject();
    }
    
    // Complète les valeurs
    $mark->mark = $this->mark;
    $mark->done = $mark->mark == "OKOKOKOK" ? '1' : '0';
    
    return $mark;
  }

  /**
   * Plus ancienne mark pour ce trigger
   *
   * @return CTriggerMark
   */
  function loadOldestMark() {
    $mark = new CTriggerMark();
    $mark->trigger_class = $this->class;
    $mark->loadMatchingObject(null, "trigger_number ASC");
    return $mark;
  }

  /**
   * Plus récente mark pour ce trigger
   *
   * @return CTriggerMark
   */
  function loadLatestMark() {
    $mark = new CTriggerMark();
    $mark->trigger_class = $this->class;
    $mark->loadMatchingObject(null, "trigger_number DESC");
    return $mark;
  }

  /**
   * Count marks older than reference trigger
   *
   * @param int $number Reference trigger number
   *
   * @return int
   */
  function countOlderMarks($number) {
    $mark = new CTriggerMark();
    $where["trigger_class"] = "= '$this->class'";
    $where["trigger_number"] = "< '$number'";
    return $mark->countList($where);
  }

  /**
   * Purge (DELETE) all marks older than specified number
   *
   * @param int $number Reference trigger number
   *
   * @return int Affected rows
   */
  function purgeOlderMarks($number) {
    $mark = new CTriggerMark();
    $ds = $mark->_spec->ds;
    $table = $mark->spec->table;
    $query = "DELETE FROM `$table`
      WHERE trigger_class = '$this->class'
      AND trigger_number < '$number'";
    $ds->exec($query);
    return $ds->affectedRows();
  }

  /**
   * Permet de notifier le fait qu'on passe une action
   *
   * @param int $rank Rank to star
   *
   * @return void
   */
  function starStatus($rank) {
    $this->markStatus($rank, "*");
  }
  
  /**
   * Trace value with given title
   * 
   * @param mixed  $value Value to trace
   * @param string $title Optional title
   *
   * @return void
   */
  function trace($value, $title = null) {
    if (self::$verbose) {
      mbTrace($value, $title);
    }
  }
  
  /**
   * Proceed the synchronisation of this mouvement
   * 
   * @param bool $mark Tell wether it shoud generate a trigger mark
   * 
   * @return bool Job-done value
   */
  function proceed($mark = true) {
    // Pre trace
    $this->trace($this->data, "Données à traiter dans le mouvement");
    $this->trace(join(" ", $this->changedFields), "Données modifiées");
    
    // Main syncing bloc 
    try {
      $this->synchronize();
      $return = true;
    } 
    catch (Exception $e) {
      if (self::$verbose) {
        exceptionHandler($e);
      }
      $return = false;
    }
    
    // Generate trigger mark
    if ($mark) {
      $this->markRow();
    }

    // Post trace
    $this->trace($this->data, "Données non traitées dans le mouvement");
    
    return $return;
  }
  
  /**
   * Synchronisation behaviour, to be redefined in child classes
   * 
   * @return void
   */
  function synchronize() {
  }
  
  /**
   * Tell whether a field has been changed in this mouvement
   * 
   * @param string [optional] Changed to a specific value, if not null
   * 
   */
  function changedField($field, $value = null) {
    $newValue = $this->data[$this->new_prefix . $field];
    return in_array($field, $this->changedFields) && ($value !== null ? $newValue == $value : true);
  }
}
