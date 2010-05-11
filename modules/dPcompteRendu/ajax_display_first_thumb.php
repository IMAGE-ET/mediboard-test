<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("phpThumb/phpthumb.class");

$compte_rendu_id = CValue::get("compte_rendu_id");
$user_id         = CValue::get("user_id", CAppUI::$user->user_id);
$nomdoc          = CValue::get("nomdoc");

$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
$file  = new CFile();
$files = $file->loadFilesForObject($compte_rendu);

if(!count($files)) {
  $margins = array(
    $compte_rendu->margin_top,
    $compte_rendu->margin_right,
    $compte_rendu->margin_bottom,
    $compte_rendu->margin_left
  );

  $page_width  = $compte_rendu->page_width;
  $page_height = $compte_rendu->page_height;
  $content = $compte_rendu->loadHTMLcontent($compte_rendu->source, "doc",'','','','','',$margins);

  $page_format = $compte_rendu->_page_format;
  $orientation = $compte_rendu->_orientation;

  if($orientation == "landscape") {
    $a = $page_width;
    $page_width = $page_height;
    $page_height = $a;
  }

  if(!$page_format) {
    $page_width  = round((72 / 2.54) * $page_width, 2);
    $page_height = round((72 / 2.54) * $page_height, 2);
    $page_format = array(0, 0, $page_width, $page_height);
  }
	
  $file = new CFile();
  $file->setObject($compte_rendu);
  $file->file_name  = $compte_rendu->nom;
  $file->file_type  = "application/pdf";
  $file->file_owner = $user_id;
  $file->fillFields();
  $file->updateFormFields();
  $file->forceDir();
	
  $htmltopdf = new CHtmlToPDF;
  $htmltopdf->generatePdf($content, 0, $page_format, $orientation, $file);
  $file->file_size = filesize($file->_file_path);
  $file->store();
}
else {
  $file = reset($files);
}

include_once "lib/phpThumb/phpThumb.config.php";

$phpThumb = new phpthumb();

if (!empty($PHPTHUMB_CONFIG)) {
  foreach ($PHPTHUMB_CONFIG as $key => $value) {
    $keyname = 'config_'.$key;
    $phpThumb->setParameter($keyname, $value);
  }
}

$phpThumb->setSourceFilename($file->_file_path);
$phpThumb->sfn = 0;
$phpThumb->w   = 138;
$phpThumb->dpi = 100;

header("Content-Type: image/".$PHPTHUMB_CONFIG["output_format"]);

$phpThumb->SetCacheFilename();
if (is_file($phpThumb->cache_filename)) {
	echo file_get_contents($phpThumb->cache_filename);
}
else {
	$phpThumb->GenerateThumbnail();
	$phpThumb->RenderToFile($phpThumb->ResolveFilenameToAbsolute($phpThumb->file));
	phpthumb_functions::EnsureDirectoryExists(dirname($phpThumb->cache_filename));
	if ((file_exists($phpThumb->cache_filename) && is_writable($phpThumb->cache_filename)) || is_writable(dirname($phpThumb->cache_filename))) {
	  $phpThumb->CleanUpCacheDirectory();
		if ($phpThumb->RenderToFile($phpThumb->cache_filename) && is_readable($phpThumb->cache_filename)) {
			chmod($phpThumb->cache_filename, 0644);
			echo file_get_contents($phpThumb->cache_filename);
    }
	}
}

?>