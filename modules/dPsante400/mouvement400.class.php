<?php

require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvement400 extends CRecordSante400 {
  protected $base = null;
  protected $table = null;
  
  protected $prodField = null;
  protected $idField = null;
  protected $typeField = null;

  public $verbose = false;
  
  public $statuses = array(null, null, null, null, null, null, null, null);
  public $cached   = array(null, null, null, null, null, null, null, null);
  
  public $status = null;
  public $rec = null;
  public $type = null;
  public $prod = null;

  function multipleLoad($marked = false, $max = 100) {
    $query = "SELECT * FROM $this->base.$this->table";
    $query .= $this->getMarkedQuery($marked);
 
    return CRecordSante400::multipleLoad($query, array(), $max, get_class($this));
  }

  function getMarkedQuery($marked) {
    if (!$this->prodField) {
      return;
    }
    
    return $marked ? 
      "\n WHERE $this->prodField != ''" : 
      "\n WHERE $this->prodField = ''";
  }

  function count($marked = false) {
    $req = new CRecordSante400();
    $query = "SELECT COUNT(*) AS TOTAL FROM $this->base.$this->table";
    $query .= $this->getMarkedQuery($marked);
    $req->query($query);

    return $req->consume("TOTAL");
  }

  function load($rec) {
    $query = "SELECT * FROM $this->base.$this->table" .
        "\n WHERE $this->idField = ?";

    $values = array (
      intval($rec),
    );    

    $this->loadOne($query, $values);
  }
    
  function markRow() {
    if (!$this->prodField) {
      return null;
    }
    
    $this->status = "";
    foreach ($this->statuses as $status) {
      $this->status .= null !== $status ? chr($status + ord("0")) : "-";    
    }

    $query = !in_array(null, $this->statuses, true) ?
      "DELETE FROM $this->base.$this->table WHERE $this->idField = ?" :
      "UPDATE $this->base.$this->table SET $this->prodField = '$this->status' WHERE $this->idField = ?";
    $values = array (
      $this->rec,
    );
    
    $rec = new CRecordSante400;
    $rec->query($query, $values);
  }
  
  function markStatus($rank, $value = null) {
    $this->statuses[$rank] = null === $value ? @$this->statuses[$rank] + 1 : $value;
  }

  function markCache($rank, $value = null) {
    $this->markStatus($rank, $value);
    $this->cached[$rank] = null === $value ? @$this->cached[$rank] + 1 : $value;
  }

  function trace($value, $title) {
    if ($this->verbose) {
      mbTrace($value, $title);
    }
  }
  
  function proceed() {
    $this->rec = $this->consume($this->idField);
    $this->prod = $this->consume($this->prodField);
    $this->type = $this->consume($this->typeField);
    
    $this->trace($this->data, "Données à traiter dans le mouvement");
    try {
      $this->synchronize();
      $return = true;
    } catch (Exception $e) {
      if ($this->verbose) {
        trigger_error($e->getMessage(), E_USER_WARNING);
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
