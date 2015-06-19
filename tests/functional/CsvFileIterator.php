<?php
class CsvFileIterator implements Iterator {
  protected $file;
  protected $key = 0;
  protected $current;
  protected $comment;

  public function __construct($file, $comment = "--") {
    $this->file = fopen(dirname(__DIR__)."/data/".$file, 'r');
    $this->comment = $comment;
  }

  public function __destruct() {
    fclose($this->file);
  }

  public function rewind() {
    rewind($this->file);
    $this->current = fgetcsv($this->file);
    if (strpos($this->current[0], $this->comment) === 0) {
      $this->current = fgetcsv($this->file);
    }
    $this->key = 0;
  }

  public function valid() {
    return !feof($this->file);
  }

  public function key() {
    return $this->key;
  }

  public function current() {
    return $this->current;
  }

  public function next() {
    $this->current = fgetcsv($this->file);
    $this->key++;
  }
}