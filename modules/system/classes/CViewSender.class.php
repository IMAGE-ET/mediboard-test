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
  public $every;
  public $active;
  public $max_archives;
  public $last_duration;
  public $last_size;
  public $multipart;
  
  // Form fields
  public $_params;
  public $_when;
  public $_active;
  public $_url;
  public $_file;
  public $_file_compressed;
  public $_files_list = array();
  
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
    $props["period"       ] = "enum list|1|2|3|4|5|6|10|15|20|30|60 notNull default|30";
    $props["every"        ] = "enum list|1|2|3|4|6|8|12|24 notNull default|1";
    $props["offset"       ] = "num min|0 notNull default|0";
    $props["active"       ] = "bool notNull default|0";
    $props["max_archives" ] = "num min|1 notNull default|10";
    $props["last_duration"] = "float";
    $props["last_size"    ] = "num pos";
    $props["multipart"    ] = "bool notNull default|0";
    
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
  
  function getActive($minute, $hour = null) {
    $period = intval($this->period);
    $offset = intval($this->offset);
    $every  = intval($this->every);
    $minute = intval($minute);
    $hour   = intval($hour);
    $minute_active = $minute % $period == $offset;
    $hour_active = $hour === null || ($hour % $every == 0);
    
    return $this->_active = $minute_active && $hour_active;
  }
  
  function makeHourPlan($minute = null) {
    $period = intval($this->period);
    $offset = intval($this->offset);
    $every  = intval($this->every );

    // Microplan on several minutes in case duration is more than 60s
    $microplan = array();
    $duration = $this->last_duration;
    while ($duration > 0) {
      $microplan[] = min($duration, 60);
      $duration -= 60;
    }

    // Hour plan
    $hour_plan = array_fill(0, 60, 0);
    foreach (range(0, 59) as $_min) {
      if ($_min % $period == $offset) {
        foreach ($microplan as $_offset => $_duration) {
          $hour_plan[($_min+$_offset) % 60] += $_duration / 60 / $every;
        }

      }
    }

    // Active
    if ($minute !== null) {
      $this->getActive($minute);
    }
    
    return $this->_hour_plan = $hour_plan;
  }

  function loadRefSendersSource() {
    /** @var CSourceToViewSender[] $senders_source */
    $senders_source = $this->loadBackRefs("sources_link");
    foreach ($senders_source as $_sender_source) {
      $_sender_source->loadRefSource()->loadRefSourceFTP();
    }

    return $this->_ref_senders_source = $senders_source;
  }

  function makeUrl($user) {
    $base = CAppUI::conf("base_url");
    
    $this->_params["login"] = "$user->user_username:$user->user_password";
    
    if ($this->multipart) {
      $this->_params["suppressHeaders"] = "1";
      $this->_params["multipart"] = "1";
    }
    else {
      $this->_params["dialog"] = "1";
      $this->_params["_aio"] = "1";
    }
    
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

    $this->_files_list = array();
    if ($this->multipart) {
      /*
       * Fichiers:
       *   $this->name/[datetime]/XXX.html
       *   $this->name/[datetime]/YYY.html
       * 
       * Archive:
       *   $this->name/archive/[datetime]/XXX.html
       *   $this->name/archive/[datetime]/YYY.html
       * 
       */
      $parts = json_decode($contents, true);

      foreach ($parts as $_part) {
        $_file = tempnam("", "view");

        if (file_put_contents($_file, base64_decode($_part["content"])) === false) {
          $chrono->stop();
          CApp::$chrono->start();

          $this->clearTempFiles();

          throw new CMbException("CViewSender-ko-file_put_contents");
        }
        
        $this->_files_list[] = array(
          "name_raw"  => $_file,
          "name_zip"  => null,
          "title"     => base64_decode($_part["title"]),
          "extension" => $_part["extension"],
        );
      }
    }
    else {
      $this->_files_list[] = array(
        "name_raw"  => $file,
        "name_zip"  => null,
        "title"     => null,
        "extension" => "html",
      );
    }
    
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
        try {
          $ftp = $source_ftp->init();
          if ($ftp->connect()) {
            foreach ($this->_files_list as $_i => $_file) {
              $basename = $this->name;
              if ($this->multipart) {
                $destination_basename = $source_ftp->fileprefix.$basename."/".$this->getDateTime()."/".$_file["title"];
              }
              else {
                $destination_basename = $source_ftp->fileprefix.$basename;
              }
              
              $compressed = $_sender_source->_ref_source->archive;
              $extension = ".".$_file["extension"];
  
              $file_name = $destination_basename.($compressed ? ".zip" : $extension);
              
              // Création de l'archive si nécessaire
              if ($compressed && !file_exists($_file["name_zip"])) {
                $this->_files_list[$_i]["name_zip"] = $_file["name_raw"].".zip";
                $_file["name_zip"] = $this->_files_list[$_i]["name_zip"];
                
                $archive = new ZipArchive();
                $archive->open($this->_files_list[$_i]["name_zip"], ZIPARCHIVE::CREATE);
                $archive->addFile($_file["name_raw"], $destination_basename.$extension);
                $archive->close();
              }
              
              // Envoi du fichier 
              $file = $compressed ? $_file["name_zip"] : $_file["name_raw"];
              
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

            // TODO: en mode multipart, gérer la rotation
            if (!$this->multipart) {
              $_sender_source->last_count = $this->archiveFile($ftp, $basename, $compressed);
            }

            $ftp->close();
          }
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
    
    foreach ($this->_files_list as $_file) {
      if ($_file["name_raw"] && file_exists($_file["name_raw"])) {
        unlink($_file["name_raw"]);
      }
      
      if ($_file["name_zip"] && file_exists($_file["name_zip"])) {
        unlink($_file["name_zip"]);
      }
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
      $datetime = $this->getDateTime();
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
  
  function getDateTime(){
    static $datetime = null;
    
    if ($datetime === null) {
      $datetime = CMbDT::format(null, "%Y-%m-%d_%H-%M-%S");
    }
    
    return $datetime;
  }
}
