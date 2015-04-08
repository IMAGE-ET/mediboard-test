<?php

/**
 * Interface des listes de choix
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$filtre = new CListeChoix();
$filtre->user_id     = CView::get("user_id", "num", true);
$filtre->function_id = CView::get("function_id", "num", true);

if (!$filtre->user_id && !$filtre->function_id) {
  $filtre->user_id = CMediusers::get()->_id;
}

$filtre->loadRefUser();
$filtre->loadRefFunction();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filtre", $filtre);

$smarty->display("vw_idx_listes.tpl");
