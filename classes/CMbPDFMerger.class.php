<?php

/**
 * $Id$
 *  
 * @category 
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CAppUI::requireLibraryFile("PDFMerger/PDFMerger");

/**
 * Classe de gestion de fusion de pdf héritant de PDFMerger
 */
class CMbPDFMerger extends PDFMerger {
  static $temp_files = array();

  function addPDF($file_path) {
    // Suppression de l'autoprint et travail sur copie temporaire (afin de ne pas altérer le document original)
    @mkdir("./tmp/pdfmerge");
    $temp_file = tempnam("./tmp/pdfmerge", "pdfmerge");
    $temp_files[] = $temp_file;
    $content = file_get_contents($file_path);
    $content = CWkHtmlToPDFConverter::removeAutoPrint($content);
    file_put_contents($temp_file, $content);
    self::$temp_files[] = $temp_file;
    parent::addPDF($temp_file);
  }

  function merge($outputmode = "browser", $outputpath = "newfile.pdf") {
    try {
      parent::merge($outputmode, $outputpath);
    }
    catch(Exception $e) {
      $this->deleteTempFiles();
      throw $e;
    }

    $this->deleteTempFiles();
  }

  function deleteTempFiles() {
    foreach (self::$temp_files as $_temp_file) {
      unlink($_temp_file);
    }
  }
}
