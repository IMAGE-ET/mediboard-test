<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CFile extends CDocumentItem {
  static $directory = null;
  
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_real_filename = null;
  var $file_name          = null;
  var $file_type          = null;
  var $file_date          = null;
  var $file_size          = null;
  var $private            = null;
  var $rotation           = null;
  
  // Form fields
  var $_extensioned   = null;
  var $_file_size     = null;
  var $_sub_dir       = null;
  var $_absolute_dir  = null;
  var $_file_path     = null;
  var $_nb_pages      = null;
  var $_old_file_path = null;
  
  // Behavior fields
  var $_rotate      = null;
  var $_rename      = null;
  var $_merge_files = null;

  // Other fields
  static $rotable_extensions = array("bmp", "gif", "jpg", "jpeg", "png", "pdf");

  // Files extensions so the pdf conversion is possible
  static $file_types =
    "cgm csv dbf dif doc docm docx dot dotm dotx
    dxf emf eps fodg fodp fods fodt hwp
    lwp met mml odp odg ods otg odf odm odt oth
    otp ots ott pct pict pot potm potx pps ppt pptm
    pptx rtf sgf sgv slk stc std sti stw svg svm sxc
    sxd sxg sxi sxm sxw txt uof uop uos uot wb2 wk1 wks
    wmf wpd wpg wps xlc xlm xls xlsb xlsm xlsx xlt xltm
    xltx xlw";
  
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
    $specs["file_size"]          = "num pos show|0";
    $specs["file_real_filename"] = "str notNull show|0";
    $specs["file_type"]          = "str";
    $specs["file_name"]          = "str notNull show|0";
    $specs["private"]            = "bool notNull default|0";
    $specs["rotation"]           = "enum list|0|90|180|270";
    // Form Fields
    $specs["_sub_dir"]      = "str";
    $specs["_absolute_dir"] = "str";
    $specs["_file_path"]    = "str";
    $specs["_file_size"]    = "str show|1";
    $specs["_old_file_path"]= "str";
    // Behavior fields
    $specs["_rotate"]       = "enum list|left|right";
    $specs["_rename"]       = "str";
    $specs["_merge_files"]  = "bool";
    return $specs;
  }
  
  function forceDir() {
    // Check global directory
    if (!CMbPath::forceDir(self::$directory)) {
      trigger_error("Files directory is not writable : " . self::$directory, E_USER_WARNING);
      return false;
    }
    
    // Checks complete file directory
    CMbPath::forceDir($this->_absolute_dir);
  }
  
  function getBinaryContent() {
    return file_get_contents($this->_file_path);
  }
  
  function putContent($filedata) {
    if (!$this->file_real_filename) {
      return false;
    }
    
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
    $result = file_put_contents($this->_file_path, $filedata);
    $this->file_size = filesize($this->_file_path);
    return $result;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_extensioned = $this->file_name;
    $this->_file_size = CMbString::toDecaBinary($this->file_size);
    
    $this->completeField("object_id");

    // Computes complete file path
    if ($this->object_id) {
      $this->completeField("file_real_filename");
      $this->_sub_dir      = "$this->object_class/" . intval($this->object_id / 1000);
      $this->_absolute_dir = self::$directory . "/$this->_sub_dir/$this->object_id";
      $this->_file_path    = "$this->_absolute_dir/$this->file_real_filename";
    }
    
    $this->_shortview = $this->_view = str_replace("_", " ", $this->file_name);
  }
  
  function getPerm($permType) {
    // Delegate on target object
    $this->loadTargetObject();
    return $this->_ref_object->_id ?
      $this->_ref_object->getPerm($permType) :
      false;
  }
  
  function fillFields(){
    if (!$this->_id) {
      if (!$this->file_date)          $this->file_date = mbDateTime();
      if (!$this->file_real_filename) $this->file_real_filename = uniqid(rand());
    }
  }
  
  function store() {
    if ($this->_id && ($this->fieldModified("object_id") || $this->fieldModified("object_class"))) {
      $this->_old->updateFormFields();
      $this->moveFile($this->_old->_file_path);
    }
    
    // Make sure filename is unique for an object
    if (!$this->_id) {
      $this->completeField("file_name");
      $this->completeField("object_class");
      $this->completeField("object_id");

      $ds = $this->_spec->ds;
      $where["object_class"] = " = '$this->object_class'";
      $where["object_id"   ] = " = '$this->object_id'";
      $where["file_name"]    = $ds->prepare("= %", $this->file_name);
      if ($this->countList($where)) {
        $last_point = strrpos($this->file_name, '.');
        $base_name = substr($this->file_name, 0, $last_point);
        $extension = substr($this->file_name, $last_point+1);
        $indice = 1;

        do {
          $indice++;
          $suffixe = sprintf(" %02s", $indice);
          $file_name = "$base_name$suffixe.$extension";
          $where["file_name"] = $ds->prepare("= %", $file_name);
          
        } 
        while ($this->countList($where));
        $this->file_name = $file_name;
      }
      
    }
    if (!$this->_id && $this->rotation === null) {
      $this->loadNbPages();
      $this->rotation = $this->rotation === null ? 0 : $this->rotation;
    }

    if ($this->_rotate !== null) {
        $this->completeField("rotation");
    }
    if ($this->_rotate == "left") {
      $this->rotation += 90;
    }
    if ($this->_rotate == "right") {
      $this->rotation -= 90;
    }
    $this->rotation %= 360;
    if ($this->rotation < 0) {
      $this->rotation += 360;
    }
    
    $this->rotation = $this->rotation % 360;
    if ($this->rotation < 0) { $this->rotation += 360; }
    return parent::store();
  }
  
  function delete() {
    // Remove previews
    $this->loadRefsFiles();
    foreach($this->_ref_files as $_file) {
      $_file->delete();
    }
    
    if ($msg = parent::delete()) {
      return $msg;
    }

    // Actually remove the file
    @unlink($this->_file_path);

    // Delete any index entries
    $query = "DELETE FROM files_index_mediboard WHERE file_id = '$this->_id'";
    if (!$this->_spec->ds->exec($query)) {
      return $this->_spec->ds->error();
    }
  }

  /**
   * move a file from a temporary (uploaded) location to the file system
   * @return boolean job-done
   */ 
  function moveTemp($file) {
    return $this->moveFile($file, true);
  }
  
  /**
   * Move a file from a location to the file system or from the PHP temp directory
   * @return boolean job-done
   */
  function moveFile($file, $uploaded = false) {
    if (!$uploaded && !is_file($file))
      return false;
      
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
  
    // Actually move any file
    if ($uploaded)
      return move_uploaded_file($file["tmp_name"], $this->_file_path);
    else
      // https://bugs.php.net/bug.php?id=50676
      // Problem while renaming across volumes : trigger the warning "Operation is not permitted" 
      return @rename($file, $this->_file_path);
  }
    
  function oldImageMagick() {
    exec("convert --version", $ret);
    if (!isset($ret[0])) {
      return;
    }

    preg_match("/ImageMagick ([0-9\.-]+)/", $ret[0], $matches);
    return $matches[1] < "6.5.8";
  }
  
  function loadNbPages(){
    if(strpos($this->file_type, "pdf") !== false && file_exists($this->_file_path)){
      // Fichier PDF Tentative de récupération
      $string_recherche = "/Count";
      $dataFile = file_get_contents($this->_file_path);
      $nb_count = substr_count($dataFile, $string_recherche);

      if ($this->oldImageMagick() && preg_match("/\/Rotate ([0-9]+)/", $dataFile, $matches)){
        $this->rotation = 360 - $matches[1];
      }
      
      if(strpos($dataFile, "%PDF-1.4") !== false && $nb_count >= 2){
        // Fichier PDF 1.4 avec plusieurs occurence
        $splitFile = preg_split("/obj\r<</", $dataFile);
        
        foreach($splitFile as $splitval){
          if(!$this->_nb_pages){
            $splitval = str_replace(array("\r", "\n"), "", $splitval);
            $position_fin = stripos($splitval, ">>");
            if($position_fin !== false){
              $splitval = substr($splitval, 0, $position_fin);
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
      }
      elseif(strpos($dataFile, "%PDF-1.3") !== false || strpos($dataFile, "%PDF-1.6") !== false || $nb_count == 1){
        // Fichier PDF 1.3 ou 1 seule occurence
        $position_count = strripos($dataFile, $string_recherche) + strlen($string_recherche);
        $nombre_temp = explode (" ", trim(substr($dataFile,$position_count, strlen($dataFile)-$position_count)), 2);
        $this->_nb_pages = intval(trim($nombre_temp[0]));
      }
      
      // Si les deux méthodes précédentes ne donnent pas de résultat
      if (is_null($this->_nb_pages)) {
        $this->_nb_pages = preg_match_all("/\/Page\W/", $dataFile, $matches);
      }
    }
  }
  
  static function loadDocItemsByObject(CMbObject $object) {
    global $can;
    if (!$object->_ref_files){
      $object->loadRefsFiles();
    }
    if (!$object->_ref_documents){
      $object->loadRefsDocs();
    }
    
    //Création du tableau des catégorie pour l'affichage
    $affichageFile = array(
      array(
        "name" => CAppUI::tr("CFilesCategory.none"),
        "items" => array(),
      )
    );
    
    foreach (CFilesCategory::listCatClass($object->_class) as $_cat) {
      $affichageFile[$_cat->_id] = array(
        "name" => $_cat->nom,
        "items" => array(),
      );
    }

    //Ajout des fichiers dans le tableau
    foreach ($object->_ref_files as $keyFile=>&$_file) {
      $cat_id = $_file->file_category_id ? $_file->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_file->file_name-$_file->_guid"] =& $_file;
      if (!isset($affichageFile[$cat_id]["name"]))
        $affichageFile[$cat_id]["name"] = '';
    }
    
    //Ajout des document dans le tableau
    foreach ($object->_ref_documents as $keyDoc=>&$_doc) {
      $cat_id = $_doc->file_category_id ? $_doc->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_doc->nom-$_doc->_guid"] =& $_doc;
      if (!isset($affichageFile[$cat_id]["name"]))
        $affichageFile[$cat_id]["name"] = '';
    }
    
    // Classement des Fichiers et des document par Ordre alphabétique
    foreach($affichageFile as $keyFile => $currFile){
      ksort($affichageFile[$keyFile]["items"]);
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
  
  function file_empty() {
    if (file_exists($this->_file_path)) {
      file_put_contents($this->_file_path, '');
    }
  }
  
  function isPDFconvertible($file_name = null) {
    if (!$file_name) {
      $file_name = $this->file_name;
    }
    return
      in_array(substr(strrchr(strtolower($file_name), '.'),1), preg_split("/[\s]+/", CFile::$file_types)) &&
      (CAppUI::conf("dPfiles CFile ooo_active") == 1);
  }
 
  static function openoffice_launched() {
    return exec("pgrep soffice");
  }
  
  static function openoffice_overload($force_restart = 0) {
    $a = exec("sh shell/ooo_overload.sh $force_restart");
  }
  
  function convertToPDF($file_path = null, $pdf_path = null) {
    global $rootName;
    
    // Vérifier si openoffice est lancé
    if (!CFile::openoffice_launched()){
      return 0;
    }
    
    // Vérifier sa charge en mémoire
    CFile::openoffice_overload();
    
    if (!$file_path && !$pdf_path) {
      $file = new CFile();
      $file->setObject($this);
      $file->private = $this->private;
      $file->file_name  = $this->file_name . ".pdf";
      $file->file_type  = "application/pdf";
      $file->author_id = CAppUI::$user->_id;
      $file->fillFields();
      $file->updateFormFields();
      $file->forceDir();
      $save_name = $this->_file_path;
      
      if ($msg = $file->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        return 0;
      }
      $file_path = $this->_file_path;
      $pdf_path  = $file->_file_path;
    }
    
    // Requête post pour la conversion.
    // Cela permet de mettre un time limit afin de garder le contrôle de la conversion.
    
    ini_set("default_socket_timeout", 10);
    
    $fileContents = base64_encode(file_get_contents($file_path));
    
    $url = CAppUI::conf("base_url")."/index.php?m=dPfiles&a=ajax_ooo_convert&suppressHeaders=1";
    $data = array (
      "file_data" => $fileContents,
      "pdf_path"  => $pdf_path
    );
    
    // Fermeture de la session afin d'écrire dans le fichier de session
    session_write_close();
    
    // Le header Connection: close permet de forcer a couper la connexion lorsque la requête est effectuée
    $ctx = stream_context_create(array(
      'http' => array(
        'method'  => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded charset=UTF-8\r\n".
                     "Connection: close\r\n".
                     "Cookie: mediboard=".session_id()."\r\n",
        'content' => http_build_query($data),
      )
    ));
    
    // La requête post réouvre la session
    $res = file_get_contents($url, false, $ctx);
    
    if (isset($file) && $res == 1) {
      $file->file_size = filesize($pdf_path);
      if ($msg = $file->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        return 0;
      }
    }
    // Si la conversion a échoué,
    // on relance le service s'il ne répond plus.
    if ( $res != 1) {
      CFile::openoffice_overload(1);
    }
    
    return $res;
  }
  
  static function convertTifPagesToPDF($tif_files){
    if (!class_exists("FPDF")){
      CAppUI::requireLibraryFile("PDFMerger/fpdf/fpdf");
    }
    
    $pngs = array();
    foreach($tif_files as $tif) {
      $pngs[] = self::convertTifToPng($tif); // "C:\\ImageMagick6.6.0-Q16\\"
    }
    
    $pdf = new FPDF();
    
    foreach($pngs as $png) {
      $pdf->AddPage();
      $pdf->Image($png, 5, 5, 200); // millimeters
    }
    
    $out = $pdf->Output("", 'S');
    
    foreach($pngs as $png) {
      unlink($png);
    }
    
    return $out;
  }
  
  static function convertTifToPng($path) {
    $tmp_tmp = tempnam(sys_get_temp_dir(), "mb_");
    unlink($tmp_tmp);
    
    $tmp  = "$tmp_tmp.png";
    
    $from = escapeshellarg($path);
    $to   = escapeshellarg($tmp);
    $exec = "convert $from $to";
    
    exec($exec, $yaks);
    
    return $tmp;
  }
  
  function loadPDFconverted() {
    $file = new CFile();
    $file->object_class = "CFile";
    $file->object_id = $this->_id;
    $file->loadMatchingObject();
    return $file;
  }
  
  function streamFile() {
    header("Pragma: ");
    header("Cache-Control: ");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    // END extra headers to resolve IE caching bug
    header("MIME-Version: 1.0");
    header("Content-length: {$this->file_size}");
    header("Content-type: $this->file_type");
    header("Accept-Ranges: bytes");
    header("Content-disposition: inline; filename=\"".$this->file_name."\"");
    
    echo file_get_contents($this->_file_path);
  }
  
  /**
   * @see parent::getUsersStats();
   */
  function getUsersStats() {
    $ds = $this->_spec->ds;
    $query = "
      SELECT 
        COUNT(`file_id`) AS `docs_count`, 
        SUM(`file_size`) AS `docs_weight`,
        `author_id` AS `owner_id`
      FROM `files_mediboard` 
      GROUP BY `owner_id`
      ORDER BY `docs_weight` DESC";
    return $ds->loadList($query);
  }
  
  /**
   * @see parent::getUsersStatsDetails();
   */
  function getUsersStatsDetails($user_ids) {
    $ds = $this->_spec->ds;
    $in_owner = $ds->prepareIn($user_ids);
    $query = "
      SELECT 
        COUNT(`file_id`) AS `docs_count`, 
        SUM(`file_size`) AS `docs_weight`, 
        `object_class`, 
        `file_category_id` AS `category_id`
      FROM `files_mediboard` 
      WHERE `author_id` $in_owner
      GROUP BY `object_class`, `category_id`";
    return $ds->loadList($query);
  }
}

// We have to replace the backslashes with slashes because of PHPthumb on Windows
CFile::$directory = str_replace('\\', '/', realpath(CAppUI::conf("dPfiles CFile upload_directory")));

?>