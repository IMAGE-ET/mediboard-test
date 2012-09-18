<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$commentaire_id = CValue::get("commentaire_id");
$date           = CValue::get("date");
$hour           = CValue::get("hour");
$salle_id       = CValue::get("salle_id");
$callback       = CValue::get("callback");

$commentaire = new CCommentairePlanning();
$commentaire->load($commentaire_id);

if (!$commentaire->_id) {
  $commentaire->debut = "$date $hour:00:00";
  $commentaire->fin   = "$date ".($hour + 1).":00:00";
  $commentaire->salle_id = $salle_id;
}

$smarty = new CSmartyDP();

$smarty->assign("commentaire", $commentaire);
$smarty->assign("callback"   , $callback);

$smarty->display("inc_edit_commentaire.tpl");
