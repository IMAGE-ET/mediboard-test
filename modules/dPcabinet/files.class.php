<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject') );

class CFile extends CMbObject {
	// DB Table key
	var $file_id = null;
	
	// DB Fields
	var $file_consultation = null;
	var $file_consultation_anesth = null;
	var $file_operation = null;
	var $file_real_filename = null;
	var $file_task = null;
	var $file_name = null;
	var $file_parent = null;
	var $file_description = null;
	var $file_type = null;
	var $file_owner = null;
	var $file_date = null;
	var $file_size = null;
	var $file_version = null;

	// Form fields
  var $_file_size;

	function CFile() {
		$this->CMbObject( 'files_mediboard', 'file_id' );
    
    //@todo : creer les types des propriétés
	}

	function check() {
	// ensure the integrity of some variables
		$this->file_id = intval( $this->file_id );
		$this->file_consultation = intval( $this->file_consultation );
		$this->file_consultation_anesth = intval( $this->file_consultation_anesth );
		$this->file_operation = intval( $this->file_operation );

		return NULL; // object is ok
	}

  function updateFormFields() {
    $bytes = $this->file_size;
    $value = $bytes;
    $unit = "o";

    $kbytes = $bytes / 1024;
    if ($kbytes >= 1) {
			$value = $kbytes;
      $unit = "Ko";
		}

    $mbytes = $kbytes / 1024;
    if ($mbytes >= 1) {
      $value = $mbytes;
      $unit = "Mo";
    }

    $gbytes = $mbytes / 1024;
    if ($gbytes >= 1) {
      $value = $gbytes;
      $unit = "Go";
    }
    
    // Value with 3 significant digits, thent the unit
    $value = round($value, $value > 99 ? 0 : $value >  9 ? 1 : 2);
    $this->_file_size = "$value $unit";
  }

	function delete() {
		global $AppUI;
	// remove the file from the file system
	    if($this->file_consultation) {
		    @unlink( "{$AppUI->cfg['root_dir']}/files/consultations/$this->file_consultation/$this->file_real_filename" );
        @unlink( "{$AppUI->cfg['root_dir']}/files/consultations2/$this->file_consultation/$this->file_real_filename" );
	    }
	    elseif($this->file_consultation_anesth) {
		    @unlink( "{$AppUI->cfg['root_dir']}/files/consultations_anesth/$this->file_consultation_anesth/$this->file_real_filename" );
	    }
	    else {
		    @unlink( "{$AppUI->cfg['root_dir']}/files/operations/$this->file_operation/$this->file_real_filename" );
	    }
	// delete any index entries
		$sql = "DELETE FROM files_index_mediboard WHERE file_id = $this->file_id";
		if (!db_exec( $sql )) {
			return db_error();
		}
	// delete the main table reference
		$sql = "DELETE FROM files_mediboard WHERE file_id = $this->file_id";
		if (!db_exec( $sql )) {
			return db_error();
		}
		return NULL;
	}

// move a file from a temporary (uploaded) location to the file system
	function moveTemp( $upload ) {
		global $AppUI;
	// check that directories are created
		if (!is_dir("{$AppUI->cfg['root_dir']}/files")) {
		    $res = mkdir( "{$AppUI->cfg['root_dir']}/files", 0777 );
		    if (!$res) {
			     return false;
			 }
		}
		if($this->file_consultation) {
		  if (!is_dir("{$AppUI->cfg['root_dir']}/files/consultations/$this->file_consultation")
       && !is_dir("{$AppUI->cfg['root_dir']}/files/consultations2/$this->file_consultation")) {
		      $res = @mbForceDirectory( "{$AppUI->cfg['root_dir']}/files/consultations/$this->file_consultation", 0777 );
          $rep = "consultations";
			   if (!$res) {
          $res = mbForceDirectory( "{$AppUI->cfg['root_dir']}/files/consultations2/$this->file_consultation", 0777 );
          $rep = "consultations2";
          if(!$res)
			       return false;
			   }
		  } else if(is_dir("{$AppUI->cfg['root_dir']}/files/consultations/$this->file_consultation")) {
        $rep = "consultations";
      } else {
        $rep = "consultations2";
      } 
		  $this->_filepath = "{$AppUI->cfg['root_dir']}/files/$rep/$this->file_consultation/$this->file_real_filename";
		}
		elseif($this->file_consultation_anesth) {
		  if (!is_dir("{$AppUI->cfg['root_dir']}/files/consultations_anesth/$this->file_consultation_anesth")) {
		      $res = mkdir( "{$AppUI->cfg['root_dir']}/files/consultations_anesth/$this->file_consultation_anesth", 0777 );
			   if (!$res) {
			       return false;
			   }
		  }
		  $this->_filepath = "{$AppUI->cfg['root_dir']}/files/consultations_anesth/$this->file_consultation_anesth/$this->file_real_filename";
		}
		else {
		  if (!is_dir("{$AppUI->cfg['root_dir']}/files/operations/$this->file_operation")) {
		      $res = mkdir( "{$AppUI->cfg['root_dir']}/files/operations/$this->file_operation", 0777 );
			   if (!$res) {
			       return false;
			   }
		  }
		  $this->_filepath = "{$AppUI->cfg['root_dir']}/files/operations/$this->file_operation/$this->file_real_filename";
		}
	// move it
		$res = move_uploaded_file( $upload['tmp_name'], $this->_filepath );
		if (!$res) {
		    return false;
		}
		return true;
	}

// parse file for indexing
	function indexStrings() {
		global $AppUI;
	// get the parser application
		$parser = @$AppUI->cfg["ft"][$this->file_type];
		if (!$parser) {
			return false;
		}
	// buffer the file
		$fp = fopen( $this->_filepath, "rb" );
		$x = fread( $fp, $this->file_size );
		fclose( $fp );
	// parse it
		$parser = $parser . " " . $this->_filepath;
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
