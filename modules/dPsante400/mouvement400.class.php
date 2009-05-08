<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("dPsante400", "recordsante400");

class CMouvement400 extends CRecordSante400 {
  public $base = null;
  public $table = null;
  
  public $markField = null;
  public $idField = null;
  public $typeField = null;
  
  public $statuses = array(null, null, null, null, null, null, null, null);
  public $cached   = array(null, null, null, null, null, null, null, null);
  
  public $status = null;
  public $rec = null;
  public $type = null;
  public $prod = null;
  public $when = null;
  public $changedFields = array();

  function initialize() {
    // Consume metadata
    $this->when = $this->consumeDateTimeFlat("TRDATE", "TRHEURE");
    $this->rec  = $this->consume($this->idField);
    $this->mark = $this->consume($this->markField);
    $this->type = $this->consume($this->typeField);
    
    // Analyse changed fields
    foreach (array_keys($this->data) as $beforeName) {
      $matches = array();
    	if (!preg_match("/B_(\w*)/i", $beforeName, $matches)) {
    	  continue;
    	}
    	
    	$name = $matches[1];
    	$afterName = "A_$name";
    	if ($this->data[$beforeName] != $this->data[$afterName]) {
    	  $this->changedFields[] = $name;
    	}
    }
    
    // Initialize prefix
    $this->valuePrefix = $this->type == "S" ? "B_": "A_";
  }
  
  /**
   * Load List of mouvements
   * @param $marked bool Will load only marked (already processed)
   * @param $max int maximum number of mouvements
   * @return array|CMouvement400
   */
  function loadList($marked = false, $max = 100) {
    $query = "SELECT * FROM $this->base.$this->table";
    $query.= $this->getMarkedClause($marked);
    $query.= $this->getFilterClause();
 
    $mouvs = CRecordSante400::multipleLoad($query, array(), $max, get_class($this));

    // Multiple checkout
    $recs = array();
    foreach ($mouvs as &$mouv) {
      $mouv->initialize();
      $recs[] = "'$mouv->rec'";
    }
    
    if (count($mouvs)) {
      if (CAppUI::conf("dPsante400 mark_row")) {
        $recs = join($recs, ",");
        
        $query = "UPDATE $mouv->base.$mouv->table " .
            "\n SET $mouv->markField = '========' " .
            "\n WHERE $mouv->idField IN ($recs)";
        
        $rec = new CRecordSante400;
        $this->trace($query, "Tracing opened mouvements");
        $rec->query($query);
      }
    }
    
    return $mouvs;
  }
  
  function getFilterClause() {
    return;
  }

  function getMarkedClause($marked) {
    if (!$this->markField) {
      return;
    }
    
    return $marked ? 
      "\n WHERE $this->markField NOT IN ('', 'OKOKOKOK')" : 
      "\n WHERE $this->markField = ''";
  }

  function count($marked = false) {
    $record = new CRecordSante400();
    $query = "SELECT COUNT(*) AS TOTAL FROM $this->base.$this->table";
    $query.= $this->getMarkedClause($marked);
    $query.= $this->getFilterClause();
    $record->query($query);
    return $record->consume("TOTAL");
  }
  
  /**
   * Load latest with former marks
   * @param int $max Max rows
   * @return array
   */
  function loadListWithFormerMark($max)  {
    $query = "SELECT * FROM $this->base.$this->table
    	WHERE $this->markField NOT IN ('', 'OKOKOKOK')";
    $query.= $this->getFilterClause();
    $query.= "\n ORDER BY $this->idField DESC";
    $mouvs = CRecordSante400::multipleLoad($query, array(), $max, get_class($this));
    foreach ($mouvs as &$mouv) {
      $mouv->initialize();
    }
        
    return $mouvs;
  }
  
  /**
   * Load latest succeded with former marks
   * @return array
   */
  function loadLatestSuccessWithFormerMark() {
    $query = "SELECT * FROM $this->base.$this->table
    	WHERE $this->markField = 'OKOKOKOK'";
    $query.= $this->getFilterClause();
    $query.= "\n ORDER BY $this->idField DESC";
    $this->loadOne($query);
    $this->initialize();
  }
    
  function load($rec) {
    $query = "SELECT * FROM $this->base.$this->table" .
        "\n WHERE $this->idField = ?";

    $values = array (
      intval($rec),
    );    

    $this->loadOne($query, $values);
    $this->initialize();

    // Checkout
    if (CAppUI::conf("dPsante400 mark_row")) {
      $query = "UPDATE $this->base.$this->table " .
          "\n SET $this->markField = '========' " .
          "\n WHERE $this->idField = ?";
      $values = array($this->rec);
      
      $rec = new CRecordSante400;
      $rec->query($query, $values);
    }
  }
  
/**
 * Mark the mouvement row
 * ======== : checked out
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
    
    $mark = new CTriggerMark();
    $mark->trigger_number = $this->rec;
    $mark->type = get_class($this);
    $mark->loadMatchingObject();
    $mark->mark = $this->status;
    if (!CAppUI::conf("dPsante400 mark_row")) {
      $mark->done = in_array(null, $this->statuses, true);
    }
    
    mbTrace($mark->getValues());

    if (!CAppUI::conf("dPsante400 mark_row")) {
      return;
    }
    
    if (!$this->markField) {
      return null;
    }
    
    // DB Fields
    
    
    $query = 
      !in_array(null, $this->statuses, true) ?
// NEVER DELETE
//      "DELETE FROM $this->base.$this->table WHERE $this->idField = ?" :
      "UPDATE $this->base.$this->table SET $this->markField = 'OKOKOKOK' WHERE $this->idField = ?" :
      "UPDATE $this->base.$this->table SET $this->markField = '$this->status' WHERE $this->idField = ?";
    $values = array (
      $this->rec,
    );
    
    $rec = new CRecordSante400;
    $rec->query($query, $values);
  }
  
  /**
   * Initialisation du status à zéro si non existant
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
   * @param int $rank
   * @param int $value valeur à mettre, incrémente le rang si null 
   */
  function markStatus($rank, $value = null) {
    $this->statuses[$rank] = null === $value ? @$this->statuses[$rank] + 1 : $value;
  }

  /**
   * Analogie à markStatus(), mais compte les objets récupérés du cache
   * @param int $rank
   * @param int $value valeur à mettre, incrémente le rang si null 
   */
    function markCache($rank, $value = null) {
    $this->markStatus($rank, $value);
    $this->cached[$rank] = null === $value ? @$this->cached[$rank] + 1 : $value;
  }
  
  /**
   * Cherche la mark pour ce mouvement et la complète
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
    if ($mark->mark == "OKOKOKOK") {
      $mark->done = '1';
    }
    
    return $mark;
  }

  /**
   * Permet de notifier le fait qu'on passe une action
   */
  function starStatus($rank) {
    $this->markStatus($rank, "*");
  }
  
  /**
   * Trace value with given title
   * @param mixed $value
   * @param string title
   */
  function trace($value, $title) {
    if (self::$verbose) {
      mbTrace($value, $title);
    }
  }
  
  function proceed() {
    $this->trace($this->data, "Données à traiter dans le mouvement");
    $this->trace(join(" ", $this->changedFields), "Données modifiées");
    
    try {
      $this->synchronize();
      $return = true;
    } catch (Exception $e) {
      if (self::$verbose) {
        exceptionHandler($e);
      }
      $return = false;
    }
    
    $this->markRow();
    $this->trace($this->data, "Données non traitées dans le mouvement");
    return $return;
  }
  
  function synchronize() {
  }
}
?>
