<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass ("mbobject"));
require_once($AppUI->getSystemClass ('mbpath'));

$filesDir = "{$AppUI->cfg['root_dir']}/files";

class CFile extends CMbObject {
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_consultation = null;
  var $file_consultation_anesth = null;
  var $file_operation = null;
  var $file_real_filename = null;
  var $file_name = null;
  
  var $file_type = null;
  var $file_owner = null;
  var $file_date = null;
  var $file_size = null;

  // Form fields
  var $_file_size = null;
  var $_sub_dir = null;
  var $_object_id = null;
  var $_file_path = null;

  function CFile() {
    $this->CMbObject( 'files_mediboard', 'file_id' );
    
    //@todo : creer les types des propriétés
  }

  function check() {
    // Ensure the integrity of some variables
    $this->file_id = intval( $this->file_id );
    $this->file_consultation = intval( $this->file_consultation );
    $this->file_consultation_anesth = intval( $this->file_consultation_anesth );
    $this->file_operation = intval( $this->file_operation );

    return NULL; // object is ok
  }

  function updateFormFields() {
    $this->_file_size = mbConvertDecaBinary($this->file_size);
    
    // Computes complete file path
    if ($object_id = $this->file_consultation       ) { $this->_sub_dir = "consultations"       ; $this->_object_id = $object_id; }
    if ($object_id = $this->file_consultation_anesth) { $this->_sub_dir = "consultations_anesth"; $this->_object_id = $object_id; }
    if ($object_id = $this->file_operation          ) { $this->_sub_dir = "operations"          ; $this->_object_id = $object_id; }
    
    if (!$this->_object_id) {
      trigger_error("No object_id associated with file (file_id = $this->file_id)", E_USER_WARNING);
    }
    
    // File path can't be computed yet because of consultations2 hack
//    $this->_file_path = "$filesDir/$this->_sub_dir/$this->_object_id/$this->file_real_filename";
  }
  
  function findFilePath() {
    global $filesDir;
    
    $this->_file_path = "$filesDir/$this->_sub_dir/$this->_object_id/$this->file_real_filename";
    if (!is_file($this->_file_path)) {
      $this->_sub_dir .= "2";    
      $this->_file_path = "$filesDir/$this->_sub_dir/$this->_object_id/$this->file_real_filename";
    }
    
    if (!is_file($this->_file_path)) {
      trigger_error("File is not reachable", E_USER_WARNING);
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
    return NULL;
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
    $fileDir = "$filesDir/$this->_sub_dir/$this->_object_id";

    if (!@CMbPath::forceDir($fileDir)) {
      $this->_sub_dir .= "2";
      $fileDir = "$filesDir/$this->_sub_dir/$this->_object_id";
      if (!CMbPath::forceDir($fileDir)) {
        trigger_error("File directory couldn't be created for file (file_id = $this->file_id)", E_USER_WARNING);
        return false;
      }
    }
    
    // Moves temp file to specific directory
    $this->_file_path = "$fileDir/$this->file_real_filename";
    return move_uploaded_file( $upload['tmp_name'], $this->_file_path);
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
    $pos = strpos( $parser, '/pdf' );
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
