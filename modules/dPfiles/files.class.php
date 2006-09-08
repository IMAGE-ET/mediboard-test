<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getSystemClass("mbpath"));
require_once($AppUI->getModuleClass("mediusers"));

$filesDir = $AppUI->cfg["root_dir"]."/files";

class CFile extends CMbObject {
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_object_id     = null;
  var $file_class         = null;
  var $file_real_filename = null;
  var $file_name          = null;
  var $file_type          = null;
  var $file_category_id   = null;
  var $file_owner         = null;
  var $file_date          = null;
  var $file_size          = null;

  // Form fields
  var $_file_size      = null;
  var $_sub_dir        = null;
  var $_file_path      = null;
  var $_nb_pages       = null;
  var $_ref_file_owner = null;

  function CFile() {
    $this->CMbObject("files_mediboard", "file_id");
    
    $this->_props["file_class"]         = "str|notNull";
    $this->_props["file_object_id"]     = "ref|notNull";
    $this->_props["file_category_id"]   = "ref";
    $this->_props["file_date"]          = "dateTime|notNull";
    $this->_props["file_size"]          = "num|pos";
    $this->_props["file_real_filename"] = "str|notNull";
  }

  function check() {
    // Ensure the integrity of some variables
    $this->file_id = intval($this->file_id);
    //$this->file_object_id = intval($this->file_object_id);
  }
  
  function loadRefsFwd(){
    $this->_ref_file_owner = new CMediusers;
    $this->_ref_file_owner->load($this->file_owner);
  }
  
  function updateFormFields() {
    global $filesDir;
    
    $this->_file_size = mbConvertDecaBinary($this->file_size);    

    if (!$this->file_object_id) {
      trigger_error("No object_id associated with file (file_id = $this->file_id)", E_USER_WARNING);
    }
    
    // Computes complete file path
    $this->_sub_dir = "$this->file_class";
    $this->_sub_dir .= "/".intval($this->file_object_id / 1000);

    $this->_file_path = "$filesDir/$this->_sub_dir/$this->file_object_id/$this->file_real_filename";
    
    $this->_shortview = $this->file_name;
    $this->_view = $this->file_name." (".$this->_file_size.")";
       
  }
  
  function delete() {
    // Actually remove the file
    @unlink($this->_file_path);

    // Delete any index entries
    $sql = "DELETE FROM files_index_mediboard WHERE file_id = $this->file_id";
    if (!db_exec($sql)) {
      return db_error();
    }
    
    // Delete the main table reference
    $sql = "DELETE FROM files_mediboard WHERE file_id = $this->file_id";
    
    if (!db_exec($sql)) {
      return db_error();
    }
    return null;
  }

  /**
   * move a file from a temporary (uploaded) location to the file system
   * @return boolean job-done
   */ 
  function moveTemp($upload) {
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
    return move_uploaded_file($upload["tmp_name"], $this->_file_path);
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
    $fp = fopen($this->_file_path, "rb");
    $x = fread($fp, $this->file_size);
    fclose($fp);

    // Parse it
    $parser = $parser . " " . $this->_file_path;
    $pos = strpos($parser, "/pdf");
    if (false !== $pos) {
      $x = `$parser -`;
    } else {
      $x = `$parser`;
    }

    // if nothing, return
    if (strlen($x) < 1) {
      return 0;
    }
  
    // remove punctuation and parse the strings
    $x = str_replace(array(".", ",", "!", "@", "(", ")"), " ", $x);
    $warr = split("[[:space:]]", $x);

    $wordarr = array();
    $nwords = count($warr);
    for($x=0; $x < $nwords; $x++) {
      $newword = $warr[$x];
      if(!ereg("[[:punct:]]", $newword)
        && strlen(trim($newword)) > 2
        && !ereg("[[:digit:]]", $newword)) {
        $wordarr[] = array("word" => $newword, "wordplace" => $x);
      }
    }
    db_exec("LOCK TABLES files_index_mediboard WRITE");
    
    // filter out common strings
    $ignore = array();
    include $AppUI->cfg["root_dir"]."/modules/dPcabinet/file_index_ignore.php";
    foreach ($ignore as $w) {
      unset($wordarr[$w]);
    }
    
    // insert the strings into the table
    while(list($key, $val) = each($wordarr)) {
      $sql = "INSERT INTO files_index_mediboard VALUES ('" . $this->file_id . "', '" . $wordarr[$key]["word"] . "', '" . $wordarr[$key]["wordplace"] . "')";
      db_exec($sql);
    }

    db_exec("UNLOCK TABLES;");
    return $nwords;
  }
  
  function loadFilesForObject($object){
    $key = $object->_tbl_key;
    $where["file_class"]     = "= '".get_class($object)."'";
    $where["file_object_id"] = "= '".$object->$key."'";
    $listFile = new CFile();
    $listFile = $listFile->loadList($where);
    return $listFile;
  }
  
  function loadNbFilesByCategory($object){
    $key = $object->_tbl_key;
    
    // Liste des Category pour les fichiers de cet objet
    $listCategory = new CFilesCategory;
    $listCategory = $listCategory->listCatClass(get_class($object));
    
    // Création du tableau de catégorie initialisé à 0
    $affichageNbFile = array();
    $affichageNbFile[0]["name"] = "Aucune Catégorie";
    $affichageNbFile[0]["nb"]   = 0;
    foreach($listCategory as $keyCat => $currCat){
      $affichageNbFile[$keyCat] = array();
      $affichageNbFile[$keyCat]["name"] = $currCat->nom;
      $affichageNbFile[$keyCat]["nb"]   = 0;
    }
   
    // S'il objet valide
    if($object->$key){
      foreach($object->_ref_files as $keyFile => $curr_file){
        $affichageNbFile[$curr_file->file_category_id]["nb"] ++;
      }
    }
    return $affichageNbFile;
  }
  
  function loadNbPages(){
    if(strpos($this->file_type, "pdf") !== false){
      // Fichier PDF Tentative de récupération
      $string_recherche = "/Count";
      $dataFile = file_get_contents($this->_file_path);
      $nb_count = substr_count($dataFile, $string_recherche);
      if(strpos($dataFile, "%PDF-1.4") !== false && $nb_count>=2){
        // Fichier PDF 1.4 avec plusieurs occurence
        $splitFile = preg_split("/obj\r<</",$dataFile);
        foreach($splitFile as $splitval){
          if(!$this->_nb_pages){
            $splitval =ereg_replace("\r","",$splitval);
            $splitval =ereg_replace("\n","",$splitval);
            $position_fin = stripos($splitval, ">>");
            if($position_fin !== false){
              $splitval = substr($splitval,0,$position_fin);
              if(strpos($splitval, "/Title") === false
                 && strpos($splitval, "/Parent") === false
                 && strpos($splitval, "/Pages") !== false
                 && strpos($splitval, $string_recherche) !== false){
                // Nombre de page ici
                $position_count = strripos($splitval, $string_recherche) + strlen($string_recherche);
                $nombre_temp = explode (" ", trim(substr($splitval,$position_count,strlen($splitval)-$position_count)) , 2 );
                $this->_nb_pages = intval(trim($nombre_temp[0]));
              }
            }
          }
        }
        
      }elseif(strpos($dataFile, "%PDF-1.3") !== false || $nb_count==1){
        // Fichier PDF 1.3 ou 1 seule occurence
        $position_count = strripos($dataFile, $string_recherche) + strlen($string_recherche);
        $nombre_temp = explode (" ", trim(substr($dataFile,$position_count,strlen($dataFile)-$position_count)) , 2 );
        $this->_nb_pages = intval(trim($nombre_temp[0]));
      }
    }
  }
  
  function canRead() {
    $this->loadRefsFwd();
    $this->_canRead = $this->_ref_file_owner->canRead();
    return $this->_canRead;
  }

  function canEdit() {
    $this->loadRefsFwd();
    $this->_canEdit = $this->_ref_file_owner->canEdit();
    return $this->_canEdit;
  }
}
?>
