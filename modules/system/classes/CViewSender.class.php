<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * View sender class. 
 * @abstract Sends the content of a view on FTP source handling :
 * - FTP source
 * - cron table-like period + offset planning
 * - rotation on destination
 */
class CViewSender extends CMbObject {
  // DB Table key
  public $sender_id;
  
  // DB fields
  public $name;
  public $description;
  public $params;
  public $period;
  public $offset;
  public $active;
  public $max_archives;
  public $last_duration;
  public $last_size;
  
  // Form fields
  public $_params;
  public $_when;
  public $_active;
  public $_url;
  public $_file;
  public $_file_compressed;
  
  // Distant properties
  public $_hour_plan;
  
  // Object references
  public $_ref_senders_source;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender";
    $spec->key   = "sender_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"         ] = "str notNull";
    $props["description"  ] = "text";
    $props["params"       ] = "text notNull";
    $props["period"       ] = "enum list|1|2|3|4|5|6|10|15|20|30|60";
    $props["offset"       ] = "num min|0 notNull default|0";
    $props["active"       ] = "bool notNull default|0";
    $props["max_archives" ] = "num min|1 notNull default|10";
    $props["last_duration"] = "float";
    $props["last_size"    ] = "num pos";
    
    $props["_url"       ] = "str";
    $props["_file"      ] = "str";

    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sources_link"] = "CSourceToViewSender sender_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->name;
    $this->_when = "$this->period mn + $this->offset";

    // Parse parameters
    $params = strtr($this->params, array("\r\n" => "&", "\n" => "&", " " => "")); 
    parse_str($params, $this->_params);
  }
  
  function getActive($minute) {
    $period = intval($this->period);
    $offset = intval($this->offset);
    $minute = intval($minute);
    
    return $this->_active = $minute % $period == $offset;
  }
  
  function makeHourPlan($minute = null) {
    $period = intval($this->period);
    $offset = intval($this->offset);

    // Hour plan
    foreach (range(0, 59) as $min) {
      $this->_hour_plan[$min] = $min % $period == $offset;
    }

    // Active
    if ($minute !== null) {
      $this->getActive($minute);
    }
    
    return $this->_hour_plan;
  }

  function loadRefSendersSource() {
    $senders_source = $this->loadBackRefs("sources_link");

    foreach ($senders_source as $_sender_source) {
      $_sender_source->loadRefSource()->loadRefSourceFTP();
    }

    return $this->_ref_senders_source = $senders_source;
  }

  function makeUrl($user) {
    $base = CAppUI::conf("base_url");
    
    $this->_params["login"] = "$user->user_username:$user->user_password";
    $this->_params["dialog"] = "1";
    $this->_params["_aio"] = "1";
    $query = CMbString::toQuery($this->_params);
    $url = "$base/?$query";
    
    return $this->_url = $url;  
  }

  function makeFile() {
    $file = tempnam("", "view");

    CApp::$chrono->stop();
    $chrono = new Chronometer();
    $chrono->start();
    
    // On récupère et écrit les données dans le fichier temporaire
    $contents = file_get_contents($this->_url);
    
    if (file_put_contents($file, $contents) === false) {
      $chrono->stop();
      CApp::$chrono->start();
      
      $this->clearTempFiles();
      
      throw new CMbException("CViewSender-ko-file_put_contents");
    }
    
    $chrono->stop();
    CApp::$chrono->start();
    
    // Trace but don't user log
    $this->_spec->loggable = false;
    $this->last_duration = $chrono->total;
    $this->last_size     = filesize($file);
    $this->store();
    
    return $this->_file = $file;
  }
  
  function sendFile() {
    // On transmet aux sources le fichier
    foreach ($this->loadRefSendersSource() as $_sender_source) {
      $_sender_source->last_datetime = CMbDT::dateTime();
      $_sender_source->last_status   = "triggered";
      $_sender_source->last_duration = null;
      $_sender_source->last_size     = null;

      $chrono = new Chronometer();
      $chrono->start();
      
      $_source = $_sender_source->_ref_source;
      $source_ftp = $_source->_ref_source_ftp;
      
      if ($source_ftp->_id && $source_ftp->active && $_source->actif) {
        $basename = $this->name;
        $destination_basename = $source_ftp->fileprefix.$basename;
        $compressed = $_sender_source->_ref_source->archive;
        
        try {
          $ftp = $source_ftp->init($source_ftp);
          if ($ftp->connect()) {
            $file_name = $destination_basename.($compressed ? ".zip" : ".html");
            
            // Création de l'archive si nécessaire
            if ($compressed && !file_exists($this->_file_compressed)) {
              $this->_file_compressed = $this->_file.".zip";
              $archive = new ZipArchive();
              $archive->open($this->_file_compressed, ZIPARCHIVE::CREATE);
              $archive->addFile($this->_file, $destination_basename.".html");
              $archive->close();
            }
            
            // Envoi du fichier 
            $file = $compressed ? $this->_file_compressed : $this->_file;
            $ftp->sendFile($file, $file_name);
            $_sender_source->last_status = "uploaded";

            // Vérification de la taille du fichier uploadé
            $_sender_source->last_size = $ftp->getSize($file_name);
            if ($_sender_source->last_size == filesize($file)) {
              $_sender_source->last_status = "checked";
            }
            
            // Enregistrement
            $source_ftp->counter++;
            $source_ftp->store();          
          }
          
          $_sender_source->last_count = $this->archiveFile($ftp, $basename, $compressed);

          $ftp->close();
        } 
        catch (Exception $e) {
          $this->clearTempFiles();
          CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
        }
      }
      
      $chrono->stop();
      $_sender_source->last_duration = $chrono->total;
      $_sender_source->store();
    }
    
    $this->clearTempFiles();
  }

  function clearTempFiles() {
    unlink($this->_file);
    
    if ($this->_file_compressed && file_exists($this->_file_compressed)) {
      unlink($this->_file_compressed);
    }
  } 
  
  /**
   * Populate archive directory up to max_archives files
   * 
   * @param CFTP    $ftp        FTP connector
   * @param string  $basename   Base name for archive directory
   * @param boolean $compressed True if file is an archive
   *
   * @return int Current archive count
   */
  function archiveFile(CFTP $ftp, $basename, $compressed) {
    try {
      // Répertoire d'archivage
      $directory = $ftp->fileprefix.$basename;
      $datetime = CMbDT::transform(null, null, "%Y-%m-%d_%H-%M-%S");
      $ftp->createDirectory($directory);
      
      // Transmission de la copie
      $archive  = "$directory/archive-$datetime". ($compressed ? ".zip" : ".html");      
      $file = $compressed ? $this->_file_compressed : $this->_file;
      $ftp->sendFile($file, $archive);
      
      // Rotation des fichiers
      $files = $ftp->getListFiles($directory);
      rsort($files);
      $list_files = array_slice($files, $this->max_archives);
      foreach ($list_files as $_file) {
        $ftp->delFile($_file);
      }
    }
    catch (CMbException $e) {
      $e->stepAjax();
    }
    
    return count($ftp->getListFiles($directory));
  }
}
