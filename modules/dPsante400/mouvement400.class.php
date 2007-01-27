<?php

require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvement400 extends CRecordSante400 {
  protected $base = null;
  protected $table = null;
  
  protected $prodField = null;
  protected $idField = null;
  protected $typeField = null;

  public $verbose = false;
  
  public $status = "--------";
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

  function load($rec = null) {
    $query = "SELECT * FROM $this->base.$this->table";
    
    if ($rec !== null) {
      $rec = intval($rec);
      $query .= "\n WHERE $this->idField = $rec";
    }
     
    $this->query($query);
  }
    
  function markRow() {
    if (!$this->prodField) {
      return null;
    }

    if (false == strpos($this->status, "-")) {
      $query = "DELETE FROM $this->base.$this->table WHERE $this->idField = $this->rec";
    } else {
      $query = "UPDATE $this->base.$this->table SET $this->prodField = '$this->status' WHERE $this->idField = $this->rec";
    }
    
    $rec = new CRecordSante400;
    $rec->query($query);
  }
  
  function markStatus($rank, $number) {
    $this->status[$rank] = $number;
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
