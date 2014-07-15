<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$file_id = CValue::post("file_id");
$content = CValue::post("svg_content");
$del     = CValue::post("del", 0);
$export  = CValue::post("export", 0);

$file = new CFile();
$file->load($file_id);
$file->bind($_POST);
if ($export) {
  $file->_id = null;
}
$file->fillFields();
$file->loadTargetObject();
$file->updateFormFields();

if ($del) {
  if ($msg = $file->delete()) {
    CAppUI::stepAjax($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::stepAjax("CFile-msg-delete", UI_MSG_OK);
  }
}
else {
  $file->file_type =  "image/fabricjs";

  if ($export) {
    $svg = new CFile();
    $svg->file_name  = $file->file_name;
    $svg->file_type = "image/svg+xml";
    $svg->author_id = $file->author_id;
    $svg->loadMatchingObject();
    $svg->fillFields();
    $svg->setObject($file->_ref_object);
    $svg->updateFormFields();
    if (strpos($svg->file_name, ".") === false) {
      $svg->file_name = $svg->file_name.".svg";
    }
    if (strpos($svg->file_name, ".fjs") !== false) {
      $svg->file_name = str_replace(".fjs", ".svg", $svg->file_name);
    }
    // @TODO : replace url by datauri

    $content = str_replace(array("&a=fileviewer", "&file_id", "&suppressHeaders"), array("&amp;a=fileviewer", "&amp;file_id", "&amp;suppressHeaders"), $content);

    if ($result = $svg->putContent(stripslashes($content))) {
      if ($msg = $svg->store()) {
        CAppUI::stepAjax($msg, UI_MSG_ERROR);
      }
      else {
        CAppUI::stepAjax("Dessin exporté avec succès", UI_MSG_OK);
      }
    }
  }
  // draft store
  else {
    if ($result = $file->putContent(stripslashes($content))) {
      // no extensio;
      if (strpos($file->file_name, ".") === false) {
        $file->file_name .= ".fjs";
      }
      if ($msg = $file->store()) {
        CAppUI::stepAjax($msg, UI_MSG_ERROR);
      }
    }
    else {
      CAppUI::stepAjax("no content", UI_MSG_ERROR);
    }
  }
}