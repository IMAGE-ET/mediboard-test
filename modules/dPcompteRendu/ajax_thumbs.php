<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

include_once('lib/phpThumb/phpThumb.config.php');
CAppUI::requireLibraryFile("phpThumb/phpthumb.class");

$file_id = CValue::get("file_id");
$index   = CValue::get("index");

$file = new CFile;
$file->load($file_id);

// Si le pdf a été supprimé ou vidé car on ferme la popup sans enregistrer
// le document, alors on ne génère pas la vignette
if (!$file->_id || !file_exists($file->_file_path) || file_get_contents($file->_file_path) == "") {
  return;
}

// Traitement de la vignette
$thumbs = new phpthumb();

// Il faut inclure manuellement la configuration dans l'objet phpThumbs
if (!empty($PHPTHUMB_CONFIG)) {
  foreach ($PHPTHUMB_CONFIG as $key => $value) {
    $keyname = 'config_'.$key;
    $thumbs->setParameter($keyname, $value);
  }
}

$thumbs->setSourceFilename($file->_file_path);
$vignette = null;

$thumbs->sfn = $index;
$thumbs->w = 138;
$thumbs->GenerateThumbnail();
$vignette = base64_encode($thumbs->IMresizedData);

echo json_encode($vignette);


?>