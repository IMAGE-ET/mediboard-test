<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

CCanDo::checkRead();

$value_selected = CValue::get("value_selected");
$codePere = CValue::get("codePere");

// On récupère les chapitres du niveau concerné
$list = array();
$query = "SELECT * FROM c_arborescence WHERE CODEPERE = '$codePere' ORDER BY RANG";

$ds     = CSQLDataSource::get("ccamV2");
$result = $ds->exec($query);
while ($row = $ds->fetchArray($result)) {
  $codeChap = $row["CODEMENU"];
  $list[$codeChap]["rang"]  = substr($row["RANG"], 4, 2);
  $list[$codeChap]["texte"] = $row["LIBELLE"];
}

$smarty = new CSmartyDP();
$smarty->assign("value_selected" , $value_selected);
$smarty->assign("list"           , $list);
$smarty->display("inc_select_codes.tpl");