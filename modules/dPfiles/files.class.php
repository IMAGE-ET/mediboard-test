<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/
global $filesDir;
$filesDir = CAppUI::conf("root_dir") . "/files";

class CFile extends CDocumentItem {
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_real_filename = null;
  var $file_name          = null;
  var $file_type          = null;
  var $file_owner         = null;
  var $file_date          = null;
  var $file_size          = null;

  // Form fields
  var $_file_size    = null;
  var $_sub_dir      = null;
  var $_absolute_dir = null;
  var $_file_path    = null;
  var $_nb_pages     = null;
  
  // References
  var $_ref_file_owner = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_mediboard';
    $spec->key   = 'file_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["documents_ged_suivi"] = "CDocGedSuivi file_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["file_date"]          = "dateTime notNull";
    $specs["file_size"]          = "num pos";
    $specs["file_real_filename"] = "str notNull";
    $specs["file_owner"]         = "ref notNull class|CMediusers";
    $specs["file_type"]          = "str";
    $specs["file_name"]          = "str notNull";
    return $specs;
  }
  
  function getContent() {
    return file_get_contents($this->_file_path);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_file_owner = new CMediusers;
    $this->_ref_file_owner->load($this->file_owner);
  }
  
  function loadView() {
    $this->loadRefsFwd();
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_extensioned = $this->file_name;
    
    global $filesDir;
    $this->_file_size = mbConvertDecaBinary($this->file_size);    

    if ($this->object_id) {
    
      // Computes complete file path
      $this->_sub_dir = "$this->object_class";
      $this->_sub_dir .= "/".intval($this->object_id / 1000);
      
      $this->_absolute_dir = "$filesDir/$this->_sub_dir/$this->object_id";

      $this->_file_path = "$filesDir/$this->_sub_dir/$this->object_id/$this->file_real_filename";
    }
    
    $this->_shortview = $this->file_name;
    $this->_view = $this->file_name;  
  }
  
  function getPerm($permType) {
    if(!$this->_ref_file_owner){
      $this->loadRefsFwd();
    }

    // Delegate on target object
    $objectPerm = $this->_ref_object->_id ?
      $this->_ref_object->getPerm($permType) :
      false;

    return $objectPerm;
  }
  
  function delete() {
    // Actually remove the file
    @unlink($this->_file_path);

    // Delete any index entries
    $sql = "DELETE FROM files_index_mediboard WHERE file_id = $this->file_id";
    if (!$this->_spec->ds->exec($sql)) {
      return $this->_spec->ds->error();
    }
    
    // Delete the main table reference
    $sql = "DELETE FROM files_mediboard WHERE file_id = $this->file_id";
    
    if (!$this->_spec->ds->exec($sql)) {
      return $this->_spec->ds->error();
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
    $fileDirComp = "$filesDir/$this->_sub_dir/$this->object_id";
    CMbPath::forceDir($fileDirComp);
    
    // Moves temp file to specific directory
    $this->_file_path = "$fileDirComp/$this->file_real_filename";
    return move_uploaded_file($upload["tmp_name"], $this->_file_path);
  }
  
  /**
   * Move a file from a location to the file system
   * @return boolean job-done
   */
  function moveFile($filename) {
    global $filesDir;
    $this->updateFormFields();
    
    // Check global directory
    if (!CMbPath::forceDir($filesDir)) {
      trigger_error("Files directory '$filesDir' is not writable", E_USER_WARNING);
      return false;
    }
    
    // Checks complete file directory
    $fileDirComp = "$filesDir/$this->_sub_dir/$this->object_id";
    CMbPath::forceDir($fileDirComp);
    
    // Moves temp file to specific directory
    $this->_file_path = "$fileDirComp/$this->file_real_filename";
    return rename($filename, $this->_file_path);
  }

  /**
   * Parse file for indexing
   */
  function indexStrings() {

    // Get the parser application
    $parser = CAppUI::conf("ft $this->file_type");
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
    $this->_spec->ds->exec("LOCK TABLES files_index_mediboard WRITE");
    
    // filter out common strings
    $ignore = array();
    include CAppUI::conf("root_dir") ."/modules/dPcabinet/file_index_ignore.php";
    foreach ($ignore as $w) {
      unset($wordarr[$w]);
    }
    
    // insert the strings into the table
    while(list($key, $val) = each($wordarr)) {
      $sql = "INSERT INTO files_index_mediboard VALUES ('" . $this->file_id . "', '" . $wordarr[$key]["word"] . "', '" . $wordarr[$key]["wordplace"] . "')";
      $this->_spec->ds->exec($sql);
    }

    $this->_spec->ds->exec("UNLOCK TABLES;");
    return $nwords;
  }
  
  function loadFilesForObject($object){
    $key = $object->_spec->key;
    $where["object_class"]     = "= '".get_class($object)."'";
    $where["object_id"] = "= '".$object->$key."'";
    $listFile = new CFile();
    $listFile = $listFile->loadList($where);
    
    foreach($listFile as $keyFile=>$currFile) {
      $listFile[$keyFile]->canRead();
      if(!$listFile[$keyFile]->_canRead){
        unset($listFile[$keyFile]);
      }
    }
    return $listFile;
  }
  
  function loadNbFilesByCategory($object){
    $key = $object->_spec->key;
    
    // Liste des Category pour les fichiers de cet objet
    $listCategory = CFilesCategory::listCatClass(get_class($object));
    
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
        if($curr_file->file_category_id){
          $affichageNbFile[$curr_file->file_category_id]["nb"] ++;
        }else{
          $affichageNbFile[0]["nb"] ++;
        }
      }
    }
    return $affichageNbFile;
  }
  
  function loadNbPages(){
    if(strpos($this->file_type, "pdf") !== false && file_exists($this->_file_path)){
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
  
  static function loadFilesAndDocsByObject($object) {
    $listCategory = CFilesCategory::listCatClass($object->_class_name);
    
    if(!$object->_ref_files){
      $object->loadRefsFiles();
    }
    if(!$object->_ref_documents){
      $object->loadRefsDocs();
    }
    
    //Création du tableau des catégorie pour l'affichage
    $affichageFile = array();
    $affichageFile[0] = array();
    $affichageFile[0]["name"] = "Aucune Catégorie";
    $affichageFile[0]["DocsAndFiles"] = array();
    foreach($listCategory as $keyCat => $curr_Cat){
      $affichageFile[$keyCat]["name"] = $curr_Cat->nom;
      $affichageFile[$keyCat]["DocsAndFiles"] = array();
    }
    
    //Ajout des fichiers dans le tableau
    foreach($object->_ref_files as $keyFile=>$FileData) {
      $object->_ref_files[$keyFile]->canRead();
      if($object->_ref_files[$keyFile]->_canRead) {
      	$id = $FileData->file_category_id ? $FileData->file_category_id : 0;
        $affichageFile[$id]["DocsAndFiles"][$FileData->file_name."_CFile_".$FileData->file_id] =& $object->_ref_files[$keyFile];
        if (!isset($affichageFile[$id]["name"]))
          $affichageFile[$id]["name"] = '';
      }
    }
    
    //Ajout des document dans le tableau
    foreach($object->_ref_documents as $keyDoc=>$DocData) {
      $object->_ref_documents[$keyDoc]->canRead();
      if($object->_ref_documents[$keyDoc]->_canRead) {
      	$id = $DocData->file_category_id ? $DocData->file_category_id : 0;
        $affichageFile[$id]["DocsAndFiles"][$DocData->nom."_CCompteRendu_".$DocData->compte_rendu_id] =& $object->_ref_documents[$keyDoc];
        if (!isset($affichageFile[$id]["name"]))
          $affichageFile[$id]["name"] = '';
      }
    }
    
    // Classement des Fichiers et des document par Ordre alphabétique
    foreach($affichageFile as $keyFile => $currFile){
      ksort($affichageFile[$keyFile]["DocsAndFiles"]);
    }

    return $affichageFile;
  }
  
  function handleSend() {
  	$this->completeField("file_name");
    $this->completeField("file_real_filename");
    $this->completeField("file_type");
    $this->completeField("file_date");
    $this->updateFormFields();
    
    return parent::handleSend();
  }
}
?>
