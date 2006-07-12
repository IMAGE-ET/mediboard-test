<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass ("mbobject"));
require_once($AppUI->getSystemClass ("mbpath"));

$filesDir = $AppUI->cfg["root_dir"]."/files";

class CFile extends CMbObject {
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_object_id = null;
  var $file_class = null;
  var $file_real_filename = null;
  var $file_name = null;
  
  var $file_type = null;
  var $file_owner = null;
  var $file_date = null;
  var $file_size = null;

  // Form fields
  var $_file_size = null;
  var $_sub_dir = null;
  var $_file_path = null;

  function CFile() {
    $this->CMbObject("files_mediboard", "file_id");
    
    //@todo : creer les types des propriétés
  }

  function check() {
    // Ensure the integrity of some variables
    $this->file_id = intval($this->file_id);
    $this->file_object_id = intval($this->file_object_id);
    return null;
  }

  function updateFormFields() {
    $this->_file_size = mbConvertDecaBinary($this->file_size);

    if (!$this->file_object_id) {
      trigger_error("No object_id associated with file (file_id = $this->file_id)", E_USER_WARNING);
    }
    
    // Computes complete file path
    $this->_sub_dir = "$this->file_class";
    $this->_sub_dir .= "/".intval($this->file_object_id / 1000);
  }
  
  function findFilePath() {
    global $filesDir;
    
    $this->_file_path = "$filesDir/$this->_sub_dir/$this->file_object_id/$this->file_real_filename";
    if (!is_file($this->_file_path)) {
      trigger_error("Fichier introuvable", E_USER_WARNING);
    }
  }

  function delete() {
    // Actually remove the file
    $this->findFilePath();
    @unlink($this->_file_path);

    // Delete any index entries
    $sql = "DELETE FROM files_index_mediboard WHERE file_id = $this->file_id";
    if (!db_exec( $sql )) {
      return db_error();
    }
    
    // Delete the main table reference
    $sql = "DELETE FROM files_mediboard WHERE file_id = $this->file_id";
    
    if (!db_exec( $sql )) {
      return db_error();
    }
    return null;
  }

  /**
   * move a file from a temporary (uploaded) location to the file system
   * @return boolean job-done
   */ 
  function moveTemp( $upload ) {
    global $filesDir;
    $this->updateFormFields();
    
    // Check global directory
    if (!CMbPath::forceDir($filesDir)) {
      trigger_error("Files directory '$filesDir' is not writable", E_USER_WARNING);
      return false;
    }
    
    // Checks complete file directory
    $fileDir = "$filesDir/$this->_sub_dir/$this->file_object_id";
    CMbPath::forceDir($fileDir);
    
    // Moves temp file to specific directory
    $this->_file_path = "$fileDir/$this->file_real_filename";
    return move_uploaded_file( $upload["tmp_name"], $this->_file_path);
  }

  /**
   * Parse file for indexing
   */
  function indexStrings() {
    global $AppUI;

    // Get the parser application
    $parser = @$AppUI->cfg["ft"][$this->file_type];
    if (!$parser) {
      return false;
    }
    
    // Buffer the file
    $fp = fopen( $this->_file_path, "rb" );
    $x = fread( $fp, $this->file_size );
    fclose( $fp );

    // Parse it
    $parser = $parser . " " . $this->_file_path;
    $pos = strpos( $parser, "/pdf" );
    if (false !== $pos) {
      $x = `$parser -`;
    } else {
      $x = `$parser`;
    }

    // if nothing, return
    if (strlen( $x ) < 1) {
      return 0;
    }
  
    // remove punctuation and parse the strings
    $x = str_replace( array( ".", ",", "!", "@", "(", ")" ), " ", $x );
    $warr = split( "[[:space:]]", $x );

    $wordarr = array();
    $nwords = count( $warr );
    for ($x=0; $x < $nwords; $x++) {
      $newword = $warr[$x];
      if (!ereg( "[[:punct:]]", $newword )
        && strlen( trim( $newword ) ) > 2
        && !ereg( "[[:digit:]]", $newword )) {
        $wordarr[] = array( "word" => $newword, "wordplace" => $x );
      }
    }
    db_exec( "LOCK TABLES files_index_mediboard WRITE" );
    
    // filter out common strings
    $ignore = array();
    include "{$AppUI->cfg['root_dir']}/modules/dPcabinet/file_index_ignore.php";
    foreach ($ignore as $w) {
      unset( $wordarr[$w] );
    }
    
    // insert the strings into the table
    while (list( $key, $val ) = each( $wordarr )) {
      $sql = "INSERT INTO files_index_mediboard VALUES ('" . $this->file_id . "', '" . $wordarr[$key]['word'] . "', '" . $wordarr[$key]['wordplace'] . "')";
      db_exec( $sql );
    }

    db_exec( "UNLOCK TABLES;" );
    return $nwords;
  }
}
?>
