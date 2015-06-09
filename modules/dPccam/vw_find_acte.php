<?php
/**
* dPccam
*
* @category Ccam
* @package  Mediboard
* @author   SARL OpenXtrem <dev@openxtrem.com>
* @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
* @version  SVN: $Id:\$
* @link     http://www.mediboard.org
*/

CCanDo::checkRead();

$page   = intval(CValue::get('page'  , 0));

// On récupère les chapitres de niveau 1
$listChap1 = array();
$query = "SELECT * FROM c_arborescence WHERE CODEPERE = '000001' ORDER BY RANG";

$ds     = CSQLDataSource::get("ccamV2");
$result = $ds->exec($query);
while ($row = $ds->fetchArray($result)) {
  $codeChap = $row["CODEMENU"];
  $listChap1[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
  $listChap1[$codeChap]["texte"] = utf8_encode($row["LIBELLE"]);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listChap1"        , $listChap1);
$smarty->assign("page"             , $page);
$smarty->display("vw_find_acte.tpl");