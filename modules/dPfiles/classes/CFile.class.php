<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Fichiers téléversés vers l'application.
 * Egalement :
 *  - pièces jointes d'email
 *  - conversion de fichiers en PDF
 *  - aperçus PDFde documents
 */
class CFile extends CDocumentItem implements IIndexableObject {
  static $directory = null;
  
  // DB Table key
  public $file_id;
  
  // DB Fields
  public $file_real_filename;
  public $file_name;
  public $file_type;
  public $file_date;
  public $rotation;
  public $language;
  public $compression;

  // Form fields
  public $_sub_dir;
  public $_absolute_dir;
  public $_file_path;
  public $_nb_pages;
  public $_old_file_path;
  public $_data_uri;
  public $_binary_content;

  public $_ref_read_status;

  // Behavior fields
  public $_rotate;
  public $_rename;
  public $_merge_files;

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

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_mediboard';
    $spec->key   = 'file_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["documents_ged_suivi"] = "CDocGedSuivi file_id";
    $backProps["mail_attachment"]     = "CMailAttachments file_id";
    $backProps["mail_content_id"]     = "CUserMail text_file_id";
    $backProps["observation_results"] = "CObservationResult file_id";
    $backProps["echanges_hl7v3"]      = "CExchangeHL7v3 object_id";
    $backProps["xds_submission_lot"]  = "CXDSSubmissionLotToDocument object_id";
    $backProps["bioserver_document"]  = "CBioserveurDocument file_id";
    $backProps["dmp_documents_sent"]  = "CDMPFile file_id";
    $backProps["file_read_status"]    = "CFileUserView file_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["file_date"]          = "dateTime notNull";
    $props["file_real_filename"] = "str notNull show|0";
    $props["file_type"]          = "str";
    $props["file_name"]          = "str notNull show|0";
    $props["rotation"]           = "num default|0 show|0";
    $props["language"]           = "enum list|en-EN|es-ES|fr-CH|fr-FR default|fr-FR show|0";
    $props["compression"]        = "str";

    // Form Fields
    $props["_sub_dir"]      = "str";
    $props["_absolute_dir"] = "str";
    $props["_file_path"]    = "str";
    $props["_old_file_path"]= "str";

    // Behavior fields
    $props["_rotate"]       = "enum list|left|right";
    $props["_rename"]       = "str";
    $props["_merge_files"]  = "bool";
    return $props;
  }

  static function massCountNamed($objects, $name) {
    $where = array();
    $where["file_name"] = " = '$name'";
    CStoredObject::massCountBackRefs($objects, "files", $where, array(), "named_file_$name");
  }

  /**
   * Load a file with a specific name associated with an object
   * 
   * @param CMbObject $object Context object
   * @param string    $name   File name with extension
   * 
   * @return CFile
   */
  static function loadNamed(CMbObject $object, $name) {
    if (!$object->_id) {
      return new CFile;
    }

    // Precounting optimization: no need to query when we already know array is empty
    $backname = "named_file_$name";

    if (isset($object->_count[$backname]) && $object->_count[$backname] === 0) {
      return new CFile();
    }

    $file = new CFile();
    $file->setObject($object);
    $file->file_name = $name;
    $file->loadMatchingObject();
    return $file;    
  }

  /**
   * Force directories creation for file upload
   *
   * @return void
   */
  function forceDir() {
    // Check global directory
    if (!CMbPath::forceDir(self::$directory)) {
      trigger_error("Files directory is not writable : " . self::$directory, E_USER_WARNING);
      return;
    }
    
    // Checks complete file directory
    CMbPath::forceDir($this->_absolute_dir);
  }

  /**
   * Get the content of the file
   *
   * @return string
   */
  function getBinaryContent() {
    return $this->_binary_content = $this->_file_path ? file_get_contents($this->_file_path) : null;
  }

  /**
   * Put a content in a file
   *
   * @param String $filedata String of content
   *
   * @return boolean
   */
  function putContent($filedata) {
    if (!$this->file_real_filename) {
      return false;
    }
    
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
    $result = file_put_contents($this->_file_path, $filedata);
    $this->doc_size = filesize($this->_file_path);
    return $result;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_extensioned = $this->file_name;

    $last_point = strrpos($this->_extensioned, '.');
    $this->_no_extension = substr($this->_extensioned, 0, $last_point);
    
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

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {

    $this->loadTargetObject();

    $parentPerm = parent::getPerm($permType);

    if ($this->_id && ($this->author_id == CMediusers::get()->_id)) {
      return $parentPerm;
    }

    if ($this->_ref_object && $this->_ref_object->_id) {
      return $parentPerm && $this->_ref_object->getPerm($permType);
    }

    return $parentPerm;
  }

  /**
   * Vérification du droit de créer un fichier au sein d'un contexte donné
   *
   * @param CMbObject $object Contexte de création du Document
   *
   * @return bool Droit de création d'un document
   */
  static function canCreate(CMbObject $object) {
    $file = new CFile();
    return $object->canRead() && $file->canClass()->edit;
  }

  /**
   * @see parent::fillFields()
   */
  function fillFields(){
    if (!$this->_id) {
      if (!$this->file_date) {
        $this->file_date = CMbDT::dateTime();
      }
      if (!$this->file_real_filename) {
        $this->file_real_filename = uniqid(rand());
      }
    }
  }

  function loadRefReadStatus($user_id = null) {
    $user_id = $user_id ? $user_id : CAppUI::$user->_id;

    $object = new CFileUserView();
    $object->file_id = $this->_id;
    $object->user_id = $user_id;
    $object->loadMatchingObject();

    return $this->_ref_read_status = $object;
  }

  /**
   * @see parent::store()
   */
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
        } while ($this->countList($where));
        $this->file_name = $file_name;
      }
      
    }
    if (!$this->_id && $this->rotation === null) {
      $this->loadNbPages();
      $this->rotation = $this->rotation === null ? 0 : $this->rotation;
      $this->rotation %= 360;
    }

    if ($this->_rotate !== null) {
      $this->completeField("rotation");

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
      if ($this->rotation < 0) {
        $this->rotation += 360;
      }
    }

    return parent::store();
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    // Remove previews
    $this->loadRefsFiles();
    foreach ($this->_ref_files as $_file) {
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

    return null;
  }

  /**
   * move a file from a temporary (uploaded) location to the file system
   *
   * @param string $file The temporary file name
   *
   * @return boolean job-done
   */
  function moveTemp($file) {
    return $this->moveFile($file, true);
  }

  /**
   * Move a file from a location to the file system or from the PHP temp directory
   *
   * @param string $file     File path
   * @param bool   $uploaded Is it an uploaded file ?
   * @param bool   $copy     Copy the file instead of moving it
   *
   * @return boolean job-done
   */
  function moveFile($file, $uploaded = false, $copy = false) {
    if (!$uploaded && !is_file($file)) {
      return false;
    }
      
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
  
    // Actually move any file
    if ($uploaded) {
      return move_uploaded_file($file["tmp_name"], $this->_file_path);
    }

    if (!$copy) {
      // https://bugs.php.net/bug.php?id=50676
      // Problem while renaming across volumes : trigger the warning "Operation is not permitted"
      return @rename($file, $this->_file_path);
    }

    return copy($file, $this->_file_path);
  }

  /**
   * Detect on old ImageMagick version on the server
   *
   * @return boolean
   */
  function oldImageMagick() {
    static $old = null;
    
    if ($old !== null) {
      return $old;
    }
    
    exec("convert --version", $ret);
    if (!isset($ret[0])) {
      return $old = false;
    }

    preg_match('/ImageMagick ([0-9\.-]+)/', $ret[0], $matches);
    return $old = $matches[1] < "6.5.8";
  }

  /**
   * Find the pages count of a pdf file
   *
   * @return void
   */
  function loadNbPages() {
    if (strpos($this->file_type, "pdf") !== false && file_exists($this->_file_path)) {
      // Fichier PDF Tentative de récupération
      $string_recherche = "/Count";
      $dataFile = file_get_contents($this->_file_path);
      $nb_count = substr_count($dataFile, $string_recherche);

      if ($this->oldImageMagick() && preg_match("/\/Rotate ([0-9]+)/", $dataFile, $matches)) {
        $this->rotation = 360 - $matches[1];
      }
      
      if (strpos($dataFile, "%PDF-1.4") !== false && $nb_count >= 2) {
        // Fichier PDF 1.4 avec plusieurs occurence
        $splitFile = preg_split("/obj\r<</", $dataFile);
        
        foreach ($splitFile as $splitval) {
          if ($this->_nb_pages) {
            break;
          }

          $splitval = str_replace(array("\r", "\n"), "", $splitval);
          $position_fin = stripos($splitval, ">>");
          if ($position_fin === false) {
            continue;
          }
          $splitval = substr($splitval, 0, $position_fin);
          if (strpos($splitval, "/Title") === false
              && strpos($splitval, "/Parent") === false
              && strpos($splitval, "/Pages") !== false
              && strpos($splitval, $string_recherche) !== false
          ) {
            // Nombre de page ici
            $position_count = strripos($splitval, $string_recherche) + strlen($string_recherche);
            $nombre_temp = explode(" ", trim(substr($splitval, $position_count, strlen($splitval)-$position_count)), 2);
            $this->_nb_pages = intval(trim($nombre_temp[0]));
          }
        }
      }
      elseif (strpos($dataFile, "%PDF-1.3") !== false || strpos($dataFile, "%PDF-1.6") !== false || $nb_count == 1) {
        // Fichier PDF 1.3 ou 1 seule occurence
        $position_count = strripos($dataFile, $string_recherche) + strlen($string_recherche);
        $nombre_temp = explode(" ", trim(substr($dataFile, $position_count, strlen($dataFile)-$position_count)), 2);
        $this->_nb_pages = intval(trim($nombre_temp[0]));
      }
      
      // Si les deux méthodes précédentes ne donnent pas de résultat
      if (is_null($this->_nb_pages)) {
        $this->_nb_pages = preg_match_all("/\/Page\W/", $dataFile, $matches);
      }
    }
  }

  /**
   * Load files for on object
   *
   * @param CMbObject $object object to load the files
   *
   * @return array[][]
   */
  static function loadDocItemsByObject(CMbObject $object) {
    if (!$object->_ref_files) {
      $object->loadRefsFiles();
    }
    if (!$object->_ref_documents) {
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
    foreach ($object->_ref_files as &$_file) {
      $cat_id = $_file->file_category_id ? $_file->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_file->file_name-$_file->_guid"] =& $_file;
      if (!isset($affichageFile[$cat_id]["name"])) {
        $affichageFile[$cat_id]["name"] = '';
      }
    }
    
    //Ajout des document dans le tableau
    foreach ($object->_ref_documents as &$_doc) {
      $_doc->isLocked();
      $cat_id = $_doc->file_category_id ? $_doc->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_doc->nom-$_doc->_guid"] =& $_doc;
      if (!isset($affichageFile[$cat_id]["name"])) {
        $affichageFile[$cat_id]["name"] = '';
      }
    }
    
    // Classement des Fichiers et des document par Ordre alphabétique
    foreach ($affichageFile as $keyFile => $currFile) {
      ksort($affichageFile[$keyFile]["items"]);
    }

    return $affichageFile;
  }

  /**
   * @see parent::handleSend()
   */
  function handleSend() {
    $this->completeField("file_name");
    $this->completeField("file_real_filename");
    $this->completeField("file_type");
    $this->completeField("file_date");
    $this->updateFormFields();
    
    return parent::handleSend();
  }

  /**
   * Empty a file
   *
   * @return void
   */
  function fileEmpty() {
    if (file_exists($this->_file_path)) {
      file_put_contents($this->_file_path, '');
    }
  }

  /**
   * Thanks to the extension, detect if a file can be PDF convertible
   *
   * @param string $file_name the name of the file
   *
   * @return bool
   */
  function isPDFconvertible($file_name = null) {
    if (!$file_name) {
      $file_name = $this->file_name;
    }
    return
      in_array(substr(strrchr(strtolower($file_name), '.'), 1), preg_split("/[\s]+/", CFile::$file_types)) &&
      (CAppUI::conf("dPfiles CFile ooo_active") == 1);
  }

  /**
   * Test the execution of the soffice process
   *
   * @return bool
   */
  static function openofficeLaunched() {
    return exec("pgrep soffice");
  }

  /**
   * Test the load of the soffice process and optionnaly can restart it
   *
   * @param int $force_restart Tell if it restarts or not
   *
   * @return void
   */
  static function openofficeOverload($force_restart = 0) {
    exec("sh shell/ooo_overload.sh $force_restart");
  }

  /**
   * PDF conversion of a file
   *
   * @param string $file_path path to the file
   * @param string $pdf_path  path the pdf file
   *
   * @return bool
   */
  function convertToPDF($file_path = null, $pdf_path = null) {
    global $rootName;
    
    // Vérifier si openoffice est lancé
    if (!CFile::openofficeLaunched()) {
      return 0;
    }
    
    // Vérifier sa charge en mémoire
    CFile::openofficeOverload();
    
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
    CSessionHandler::writeClose();
    
    // Le header Connection: close permet de forcer a couper la connexion lorsque la requête est effectuée
    $ctx = stream_context_create(
      array(
        'http' => array(
          'method'  => 'POST',
          'header'  => "Content-type: application/x-www-form-urlencoded charset=UTF-8\r\n".
                       "Connection: close\r\n".
                       "Cookie: mediboard=".session_id()."\r\n",
          'content' => http_build_query($data),
        )
      )
    );
    
    // La requête post réouvre la session
    $res = file_get_contents($url, false, $ctx);
    
    if (isset($file) && $res == 1) {
      $file->doc_size = filesize($pdf_path);
      if ($msg = $file->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
        return 0;
      }
    }
    // Si la conversion a échoué
    // on relance le service s'il ne répond plus.
    if ( $res != 1) {
      CFile::openofficeOverload(1);
    }
    
    return $res;
  }

  /**
   * Convert tif files to pdf
   *
   * @param array[] $tif_files array of tif files
   *
   * @return bool
   */
  static function convertTifPagesToPDF($tif_files){
    if (!class_exists("FPDF")) {
      CAppUI::requireLibraryFile("PDFMerger/fpdf/fpdf");
    }
    
    $pngs = array();
    foreach ($tif_files as $tif) {
      $pngs[] = self::convertTifToPng($tif); // "C:\\ImageMagick6.6.0-Q16\\"
    }
    
    $pdf = new FPDF();
    
    foreach ($pngs as $png) {
      $pdf->AddPage();
      $pdf->Image($png, 5, 5, 200); // millimeters
    }
    
    $out = $pdf->Output("", 'S');
    
    foreach ($pngs as $png) {
      unlink($png);
    }
    
    return $out;
  }

  /**
   * Convert a tif to a png
   *
   * @param string $path path to the tif file
   *
   * @return string
   */
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

  /**
   * Load a pdf file conversion
   *
   * @return CFile
   */
  function loadPDFconverted() {
    $file = new CFile();
    $file->object_class = "CFile";
    $file->object_id = $this->_id;
    $file->loadMatchingObject();
    return $file;
  }

  /**
   * Return the data uri's content of a file
   *
   * @return string
   */
  function getDataURI() {
    return $this->_data_uri = $this->_file_path ?
      "data:".$this->file_type.";base64,".urlencode(base64_encode(file_get_contents($this->_file_path))) :
      "";
  }

  /**
   * Stream a file to a client
   *
   * @return string
   */
  function streamFile() {
    header("Pragma: ");
    header("Cache-Control: ");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    // END extra headers to resolve IE caching bug
    header("MIME-Version: 1.0");
    header("Content-length: {$this->doc_size}");
    header("Content-type: $this->file_type");
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
        SUM(`doc_size`) AS `docs_weight`,
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

    $query = new CRequest();
    $query->addColumn("COUNT(`file_id`)", "docs_count");
    $query->addColumn("SUM(`doc_size`)", "docs_weight");
    $query->addColumn("object_class");
    $query->addColumn("file_category_id", "category_id");
    $query->addTable("files_mediboard");
    $query->addGroup("object_class, category_id");

    if (is_array($user_ids)) {
      $in_owner = $ds->prepareIn($user_ids);
      $query->addWhereClause("author_id", $in_owner);
    }

    return $ds->loadList($query->makeSelect());
  }

  /**
   * @see parent::getPeriodicalStatsDetails();
   */
  function getPeriodicalStatsDetails($user_ids, $depth = 8) {
    $period_types = array(
      "yearly" => array(
        "format"   => "%Y",
        "unit"     => "YEAR",
      ),
      "monthly" => array(
        "format"   => "%m/%Y",
        "unit"     => "MONTH",
      ),
      "weekly" => array(
        "format"   => "%Y S%U",
        "unit"     => "WEEK",
      ),
      "daily" => array(
        "format"   => "%d/%m",
        "unit"     => "DAY",
      ),
      "hourly" => array(
        "format"   => "%d %Hh",
        "unit"     => "HOUR",
      ),
    );

    $details = array();

    $now = CMbDT::dateTime();
    $doc = new self;
    $ds = $doc->_spec->ds;
    $deeper = $depth + 1;

    foreach ($period_types as $_type => $_period_info) {
      $format = $_period_info["format"];
      $unit   = $_period_info["unit"];

      $request = new CRequest();
      $request->addColumn("DATE_FORMAT(`file_date`, '$format')", "period");
      $request->addColumn("COUNT(`file_id`)", "count");
      $request->addColumn("SUM(`doc_size`)", "weight");
      $date_min = CMbDT::dateTime("- $deeper $unit", $now);
      $request->addWhereClause("file_date", " > '$date_min'");
      if (count($user_ids)) {
        $request->addWhereClause("author_id", CSQLDataSource::prepareIn($user_ids));
      }
      $request->addGroup("period");
      $results = $ds->loadHashAssoc($request->makeSelect($doc));

      foreach(range($depth, 0) as $i) {
        $period = CMbDT::transform("-$i $unit", $now, $format);
        $details[$_type][$period] = isset($results[$period]) ? $results[$period] : 0;
      }
    }

    return $details;
   }

  /**
   * check if this is an image
   *
   * @return bool
   */
  function isImage() {
    if (strpos($this->file_type, "image") === 0) {
      return true;
    }
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return string
   */
  function getIndexablePatient() {
    $object = $this->loadTargetObject();

    if ($object instanceof CPatient) {
      return $object->_id;
    }

    if (!method_exists($this, "loadRelPatient")) {
      $object->loadRefPatient();
    }
    else {
      $object->loadRelPatient();
    }

    switch ($this->object_class) {
      case "CConsultAnesth":
        return $object->_ref_consultation->_ref_patient;

        break;

      default:
        return $object->_ref_patient;
    }
  }

  /**
   * Get the praticien of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien() {
    $object = $this->loadTargetObject();
    if ($object instanceof CConsultAnesth) {
      $prat = $object->loadRefConsultation()->loadRefPraticien();
    }
    else {
      $prat = $object->loadRefPraticien();
    }

    return $prat;
  }

  /**
   * Loads the related fields for indexing datum
   *
   * @return array
   */
  function getIndexableData() {

    $prat               = $this->getIndexablePraticien();
    $array["id"]        = $this->_id;
    $array["author_id"] = $this->author_id;
    $array["prat_id"]   = $prat->_id;
    $array["title"]     = utf8_encode($this->file_name);
    $array["body"]             = $this->getIndexableBody($this->_absolute_dir);
    $date                      = $this->file_date;
    $array["date"]             = str_replace("-", "/", $date);
    $array["function_id"]      = $prat->function_id;
    $array["group_id"]         = "";
    $array["patient_id"]       = $this->getIndexablePatient()->_id;
    $array["object_ref_id"]    = $this->loadTargetObject()->_id;
    $array["object_ref_class"] = $this->loadTargetObject()->_class;
    $array["path"]             = $this->_file_path;
    $array["content_type"]     = $this->file_type;
    return $array;
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody($content) {
    if (substr($this->file_type, 0, 6) == 'image/') {
      $body = $this->file_name;
    }
    else {
      switch ($this->file_type) {
        case 'text/osoft' :
          $body = $this->getBinaryContent();
          $osoft_histo      = new COsoftHistorique(false);
          $body = $osoft_histo->toHTML($body);
          $body = strip_tags($body);
          break;
        case 'application/osoft' :
          $body = $this->getBinaryContent();
          $osoft_dossier    = new COsoftDossier(false);
          $body = $osoft_dossier->toHTML($body);
          $body = strip_tags($body);
          break;

        default : $body = new CSearchFileWrapper($content, $this->_id);
        $body = $body->getPlainText();

      }
    }

    return $body;
  }
}

// We have to replace the backslashes with slashes because of PHPthumb on Windows
CFile::$directory = str_replace('\\', '/', realpath(CAppUI::conf("dPfiles CFile upload_directory")));