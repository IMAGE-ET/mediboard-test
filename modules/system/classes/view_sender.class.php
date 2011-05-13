<?php /* $Id: message.class.php 8208 2010-03-04 19:14:03Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  var $sender_id = null; 
  
  // DB fields
  var $source_id   = null;
  var $name        = null;
  var $description = null;
  var $params      = null;
  var $period      = null;
  var $offset      = null;
	var $active      = null;
  
  // Form fields
	var $_params = null;
	var $_when   = null;
	var $_active = null;
  var $_url    = null;
  var $_file   = null;
  
  var $_file_download_duration = null;
  var $_file_download_size     = null;
  var $_files_upload_stats     = array();
  	
  // Distant properties
	var $_hour_plan = null;
  
  // Object references
  var $_ref_senders_source;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender";
    $spec->key   = "sender_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"       ] = "str notNull";
    $props["description"] = "text";
    $props["params"     ] = "text notNull";
    $props["period"     ] = "enum list|1|2|3|4|5|6|10|15|20|30";
    $props["offset"     ] = "num min|0 notNull default|0";
    $props["active"     ] = "bool notNull default|0";
    
    $props["_url"       ] = "str";
    $props["_file"      ] = "str";
    $props["_file_download_duration" ] = "str";
    $props["_file_download_size"     ] = "str";

    return $props;
  }

  function getBackProps() {
    return parent::getBackProps() + array(
      "sources_link" => "CSourceToViewSender sender_id",
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view         = $this->name;
		$this->_params       = explode("&", $this->params);
		$this->_when         = "$this->period mn + $this->offset";
  }
	
  function getActive($minute) {
    $period = intval($this->period);
    $offset = intval($this->offset);
    $minute = intval($minute);
  	
    return $this->_active =  $minute % $period == $offset;
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
	  $source_to_vw_sender = new CSourceToViewSender();
    $source_to_vw_sender->sender_id = $this->_id;

    $this->_ref_senders_source = array();
    foreach ($source_to_vw_sender->loadMatchingList() as $_sender_source) {
      $_sender_source->loadRefSource();
      $_sender_source->_ref_source->loadRefSourceFTP();
      $this->_ref_senders_source[] = $_sender_source;
    }

    return $this->_ref_senders_source;
	}

	function makeUrl($user) {
    $base = CAppUI::conf("base_url");
    $params = array();
    parse_str(strtr($this->params, "\n", "&"), $params);
    $params["login"] = "1";
    $params["username"] = $user->user_username;
    $params["password"] = $user->user_password;
    $params["dialog"] = "1";
    $params["_aio"] = "1";
    $query = CMbString::toQuery($params);
    $url = "$base/?$query";
    
    return $this->_url = $url;  
	}

  function makeFile() {
    global $phpChrono;
    
  	$file = tempnam("", "view");
  	
    $phpChrono->stop();
    
    $chrono = new Chronometer();
    $chrono->start();
    
  	// On récupère et écrit les données dans le fichier temporaire
  	$contents = file_get_contents($this->_url);
    if (file_put_contents($file, $contents) === false) {
      throw new CMbException("CViewSender-ko-file_put_contents");
    }
    $chrono->stop();
    $phpChrono->start();
    
    $this->_file_download_duration = $chrono->total;
    $this->_file_download_size     = CMbString::toDecaBinary(filesize($file));
    
  	return $this->_file = $file;
  }
  
  function sendFile() {
    global $phpChrono;
       
    $phpChrono->stop();
    
    // On transmet aux sources le fichier
    foreach($this->loadRefSendersSource() as $_sender_source) {
      $this->_files_upload_stats[$_sender_source->_id] = array(
        "duration" => 0,
        "status"   => false,
        "size"     => 0,
      );
  
      $chrono = new Chronometer();
      $chrono->start();
      
      $this->_files_upload_stats[$_sender_source->_id]["status"] = false; 
      
      $source_ftp = $_sender_source->_ref_source->_ref_source_ftp;
      if ($source_ftp->_id && $source_ftp->active) {     
        $basename = $this->name;
        $destination_basename = $source_ftp->fileprefix.$basename;
        
        try {
          $ftp = $source_ftp->init($source_ftp);
          if ($ftp->connect()) {
            $this->_files_upload_stats[$_sender_source->_id]["status"] = $ftp->sendFile($this->_file, "$destination_basename.html");
            $this->_files_upload_stats[$_sender_source->_id]["size"]   = $ftp->getSize("$destination_basename.html");
            
            $source_ftp->counter++;
            $source_ftp->store();          
          }
          
          $this->archiveFile($ftp, $source_ftp, $basename);

          $ftp->close();
        } catch(Exception $e) {}
      }
      
      $chrono->stop();
      $this->_files_upload_stats[$_sender_source->_id]["duration"] = $chrono->total;
    }
    
    $phpChrono->start();
    
    unlink($this->_file);
  }
  
  function archiveFile(CFTP $ftp, CSourceFTP $source_ftp, $basename) {
  	// Répertoire d'archivage
  	$directory = $ftp->fileprefix.$basename;
  	$datetime = mbTransformTime(null, null, "%Y-%m-%d_%H-%M-%S");   
    $ftp->createDirectory($directory);
  	
    // Transmission de la copie
    $archive  = "$directory/archive-$datetime.html";
    $ftp->sendFile($this->_file, $archive);
    
    // Rotation des 10 fichiers
    $files = $ftp->getListFiles($directory);
    rsort($files);
    foreach (array_slice($files, 10) as $_file) {
      $ftp->delFile($_file);
    }
  }
}

?>