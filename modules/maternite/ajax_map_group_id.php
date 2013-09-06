<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCAnDo::checkAdmin();

$limit = CValue::get("limit", 100);

$grossesse = new CGrossesse();

$where = array();
$where["group_id"] = "IS NULL";

/** @var CGrossesse[] $grossesses */
$grossesses = $grossesse->loadList($where, null, $limit);

foreach ($grossesses as $_grossesse) {
  /** @var CSejour $sejour */
  $sejour = reset($_grossesse->loadRefsSejours());

  if ($sejour) {
    $_grossesse->group_id = $sejour->group_id;
  }

  if (!$_grossesse->group_id) {
    /** @var CConsultation $consult */
    $consult = reset($_grossesse->loadRefsConsultations());

    if ($consult) {
      $_grossesse->group_id = $consult->loadRefPraticien()->loadRefFunction()->group_id;
    }

    if (!$_grossesse->group_id) {
      $_grossesse->group_id = $_grossesse->loadFirstLog()->loadRefUser()->loadRefMediuser()->loadRefFunction()->group_id;
    }
  }

  $msg = $_grossesse->store();
  CAppUI::setMsg($msg ? $msg : CAppUI::tr("CGrossesse-msg-modify"), $msg ? UI_MSG_ERROR : UI_MSG_OK);
}

echo CAppUI::getMsg();