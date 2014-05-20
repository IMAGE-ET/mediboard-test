<?php 

/**
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$limit = CValue::get("limit", "100");

$compte_rendu = new CCompteRendu();

$where = array();
$where["factory"] = "IS NULL";

$compte_rendus = $compte_rendu->loadList($where, null, $limit);

$count = count($compte_rendus);
$errors = 0;
$msgs = array();

CMbObject::massLoadBackRefs($compte_rendus, "files");

foreach ($compte_rendus as $_compte_rendu) {
  $file = $_compte_rendu->loadFile();
  $file_content = $file->_file_path && file_exists($file->_file_path) ? file_get_contents($file->_file_path) : "";

  if ($file_content === "") {
    $_compte_rendu->factory = "none";
  }
  else {
    $_compte_rendu->factory =
      strpos($file_content, "/Creator (DOMPDF)") !== false ? "CDomPDFConverter" : "CWkHtmlToPDFConverter";
  }

  if ($msg = $_compte_rendu->store()) {
    $msgs[] = $msg;
    $errors++;
  }
}

CAppUI::stepAjax("$errors erreurs sur $count compte-rendus");

foreach ($msgs as $_msg) {
  CAppUI::stepAjax($_msg);
}
